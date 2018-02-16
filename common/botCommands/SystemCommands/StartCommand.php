<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use common\models\Moderator;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 * Bind Moderators via Telegrm deep linking feature.
 */
class StartCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'start';

    /**
     * @var string
     */
    protected $description = 'Start command';

    /**
     * @var string
     */
    protected $usage = '/start or /start <token>';

    /**
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $message = $this->getMessage();
        $token  = trim($message->getText(true));

        if ($token) {
            $this->deepLink($token);
        }

        $text = 'Hi, man!' . PHP_EOL 
              . 'Type /help to see all commands!';

        $data = [
            'chat_id' => $message->getChat()->getId(),
            'text'    => $text,
        ];
        return Request::sendMessage($data);
    }

    /**
     * Binds Moderator and Telegram user via 
     * Telegram Bot Api deep linking mechanism.
     * 
     * @param string $auth_token 
     * @return bool
     */
    private function deepLink(string $auth_token) : bool
    {
        $from = $this->getMessage()->getFrom();

        $moderator = Moderator::findOne(['auth_token' => $auth_token]);
        if ($moderator === null) {
            $data = [
                'chat_id' => $from->getId(),
                'text'    => 'Error: not valid auth_token',
            ];
            Request::sendMessage($data);
            return true;
        }

        $moderator->tg_id         = $from->getId();
        $moderator->tg_username   = $from->getUsername();
        $moderator->tg_first_name = $from->getFirstName();
        $moderator->tg_last_name  = $from->getLastName();
        
        if ($moderator->save()) {
            return true;
        }

        return false;
    }
}

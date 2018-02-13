<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use common\models\Moderator;

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
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


    private function deepLink(string $token) : bool
    {
        $from = $this->getMessage()->getFrom();

        $moderator = Moderator::findOne(['auth_token' => $token]);
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

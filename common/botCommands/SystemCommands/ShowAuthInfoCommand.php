<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * ShowAuthInfo command
 *
 * Show not authenticated users info about authentification procedure.
 */
class ShowAuthInfoCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'showauthinfo';

    /**
     * @var string
     */
    protected $description = 'Show authentification info command';

    /**
     * @var string
     */
    protected $usage = '/showauthinfo';

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
        $text = 'Before you get started, please enter your auth_token with /start command:' . PHP_EOL 
            . '/start <token>';

        $data = [
            'chat_id' => $this->getMessage()->getChat()->getId(),
            'text'    => $text,
        ];

        Request::sendMessage($data);
        die();
    }
}

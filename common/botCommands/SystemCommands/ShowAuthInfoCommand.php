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

/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
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

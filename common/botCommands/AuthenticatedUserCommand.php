<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands;

use common\models\Moderator;
use Longman\TelegramBot\Request;


abstract class AuthenticatedUserCommand extends UserCommand
{
    /*
    * Make sure this command not visible in /help command.
    *
    * @var bool
    */
    public $hidden = false;

    protected $chat;
    protected $chat_id;
    protected $message;
    protected $message_id;
    protected $callback_query;
    protected $callback_query_id;
    protected $callback_query_data;
	protected $moderator;

	public function __construct(...$params)
    {
        parent::__construct(...$params);

        $this->callback_query    = $this->getCallbackQuery();

        if ($this->callback_query) {
            $this->callback_query_id   = $this->callback_query->getId();
            $this->callback_query_data = $this->callback_query->getData();
            $this->message             = $this->callback_query->getMessage();
        } else {
            $this->message             = $this->getMessage();            
        }
        $this->message_id        = $this->message->getMessageId();
        $this->chat              = $this->message->getChat();
        $this->chat_id           = $this->chat->getId();

        $this->moderator = Moderator::findOne(['tg_id'=>$this->chat_id]);

        if ($this->moderator === null) {
            $this->telegram->executeCommand('showauthinfo');
        }
    }


}
<?php

namespace Longman\TelegramBot\Commands;

use common\models\Moderator;
use Longman\TelegramBot\Request;

/**
 * Authenticated User Command
 */
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

    /**
     * @var common\models\Moderator;
     */
	protected $moderator;

    /**
     * Class constructor
     * 
     * @param mixed ...$params
     */
	public function __construct(...$params)
    {
        parent::__construct(...$params);

        $this->callback_query    = $this->getCallbackQuery();

        if ($this->callback_query) {
            $this->callback_query_id   = $this->callback_query->getId();
            $this->callback_query_data = $this->callback_query->getData();
            $this->message             = $this->callback_query->getMessage();
        } else {
            $this->message             = $this->getMessage() ?: $this->getEditedMessage();
        }
        $this->message_id        = $this->message->getMessageId();
        $this->chat              = $this->message->getChat();
        $this->chat_id           = $this->chat->getId();

        $this->moderator = Moderator::findOne(['tg_id'=>$this->chat_id]);

        if ($this->moderator === null) {
            return $this->telegram->executeCommand('login');
        }
    }


}
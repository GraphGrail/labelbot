<?php

namespace Longman\TelegramBot\Commands\UserCommands;

require_once 'AuthenticatedUserCommand.php';

use Longman\TelegramBot\Commands\AuthenticatedUserCommand;
use Longman\TelegramBot\Request;
use common\components\CallbackData;
use common\models\Label;
use common\models\AssignedLabel;
use common\components\LabelsKeyboard;


/**
 * BackKey callback command
 *
 * This command handles callback queries with BACK_KEY_PRESSED type.
 *
 * @see CallbackqueryCommand.php
 */
class BackKeyCallbackCommand extends AuthenticatedUserCommand
{
    /**
     * @var string
     */
    protected $name = 'backkeycallback';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $usage = '/backkeycallback';

    /**
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * @var bool
     */
    public $hidden = true;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $callback_data = new CallbackData($this->moderator, $this->callback_query_data);
        $verified_callback_data = $callback_data->getVerifiedData();
        list($assigned_label_id, $label_id) = explode(':', $verified_callback_data);

        $assignedLabel = AssignedLabel::findOne($assigned_label_id);
        $root_label    = Label::findOne($label_id);

        $inline_keyboard = new LabelsKeyboard($root_label, $assignedLabel, $this->moderator);
        $req_data = [
            'chat_id'      => $this->chat_id,
            'message_id'   => $this->message_id,
            'text'         => $this->message->getText(),
            'reply_markup' => $inline_keyboard->generate(),
        ];
        return Request::editMessageText($req_data);
    }
}

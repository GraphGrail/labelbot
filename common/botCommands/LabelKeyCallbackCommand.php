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
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class LabelKeyCallbackCommand extends AuthenticatedUserCommand
{
    /**
     * @var string
     */
    protected $name = 'labelkeycallback';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string
     */
    protected $usage = '/labelkeycallback';

    /**
     * @var string
     */
    protected $version = '0.1.0';

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * @var int
     */
    protected $assigned_label;

    /**
     * @var int
     */
    protected $label_id;

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
        list($assigned_label_id, $this->label_id) = explode(':', $verified_callback_data);
        
        $this->assigned_label = AssignedLabel::findOne($assigned_label_id);
        if ($this->assigned_label === null || $this->assigned_label->status !== AssignedLabel::STATUS_IN_HAND) {
            $req_data = [
                'callback_query_id' => $this->callback_query_id,
                'text'              => 'Error: label was not confirmed',
                'show_alert'        => false,
                'cache_time'        => 0,
            ];
            Request::answerCallbackQuery($req_data);
            return $this->telegram->executeCommand('get');
        }

        if ($this->labelHasChildrenLabels() || $this->labelWasAssignedEalier()) return;

        $this->assigned_label->status = AssignedLabel::STATUS_READY;            
        $this->assigned_label->label_id = $this->label_id;

        if (!$this->assigned_label->save()) {
            // TODO: log error
        }

        return $this->telegram->executeCommand('get');
    }

    /**
     * Handle if chosen label has children labels
     * 
     * @return bool
     */
    private function labelHasChildrenLabels() : bool
    {
        $root_label = Label::findOne($this->label_id);

        if ($root_label && $root_label->children) {
            $inline_keyboard = new LabelsKeyboard($root_label, $this->assigned_label, $this->moderator);
            $req_data = [
                'chat_id'      => $this->chat_id,
                'message_id'   => $this->message_id,
                'text'         => $this->message->getText(),
                'reply_markup' => $inline_keyboard->generate(),
            ];

            Request::editMessageText($req_data);
            return true;
        }
        return false;
    }

    /**
     * Handle if chosen label already was assigned by this moderator
     * 
     * @return bool
     */
    private function labelWasAssignedEalier()
    {
        if (in_array($this->assigned_label->status, [AssignedLabel::STATUS_READY, AssignedLabel::STATUS_APPROVED, AssignedLabel::STATUS_DECLINED])) {
            $req_data = [
                'callback_query_id' => $this->callback_query_id,
                'text'              => 'This data was labeled already',
                'show_alert'        => false,
                'cache_time'        => 0,
            ];
            Request::answerCallbackQuery($req_data);
            $this->telegram->executeCommand('get');
            return true;
        }
        return false;      
    }   
}

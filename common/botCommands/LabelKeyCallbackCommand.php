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
    protected $data_id;

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
        list($this->data_id, $this->label_id) = explode(':', $verified_callback_data);

        if ($this->labelHasChildrenLabels() || $this->labelWasAssignedEalier()) return;

        $assignedLabel = AssignedLabel::findOne([
            'data_id'       => $this->data_id,
            'moderator_id'  => $this->moderator->id,
            'status'        => AssignedLabel::STATUS_IN_HAND
        ]);

        if ($assignedLabel === null) {
            $req_data = [
                'callback_query_id' => $this->callback_query_id,
                'text'              => 'Error: label was not confirmed',
                'show_alert'        => false,
                'cache_time'        => 0,
            ];
            Request::answerCallbackQuery($req_data);
            return $this->telegram->executeCommand('get');
        }

        $assignedLabel->status = AssignedLabel::STATUS_READY;            
        $assignedLabel->label_id = $this->label_id;

        if (!$assignedLabel->save()) {
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
            $inline_keyboard = new LabelsKeyboard($root_label, $this->data_id, $this->moderator);
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
        $earlierAssignedLabel = AssignedLabel::find()
            ->where([
                'data_id'      => $this->data_id,
                'moderator_id' => $this->moderator->id            
            ])
            ->andWhere(new \yii\db\Expression('label_id IS NOT NULL'))
            ->one();

        if ($earlierAssignedLabel) {
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

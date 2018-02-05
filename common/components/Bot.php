<?php

namespace common\components;

use Yii;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Command;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Message;

use common\models\Data;
use common\models\LabelGroup;
use common\models\Label;
use common\models\AssignedLabel;
use common\models\Moderator;
use common\components\CallbackData;


class Bot extends yii\base\BaseObject
{
    public $cmd;
    public $chat;
    public $chat_id;
    public $message;
    public $message_id;
    public $callback_query;
    public $callback_query_id;
    public $callback_query_data;
    public $moderator;

    public function __construct($cmd, $config = [])
    {
        $this->cmd               = $cmd;
        $this->callback_query    = $cmd->getCallbackQuery();

        if ($this->callback_query) {
            $this->callback_query_id   = $this->callback_query->getId();
            $this->callback_query_data = $this->callback_query->getData();
            $this->message             = $this->callback_query->getMessage();
        } else {
            $this->message             = $cmd->getMessage();            
        }
        $this->message_id        = $this->message->getMessageId();
        $this->chat              = $this->message->getChat();
        $this->chat_id           = $this->chat->getId();
        $this->moderator         = Moderator::findOne(['tg_id'=>$this->chat_id]);

        if ($this->moderator === null) {
            // TODO: need auth!!
        }

        parent::__construct($config);
    }


    public function sendData(int $edit_message_id=0)
    {
        $data = Data::getForLabelAssignment();
        // TODO: We need to delete data with empty texts on Dataset upload,
        // because Telegram don't send/edit message with empty text!!
        if (!trim($data->data)) {
            $data->data = 'no data';
        }
        // For now, we just get the first labelGroup for dataset
        $labelGroup = $data->dataset->labelGroups[0];

        $inline_keyboard = $this->generateLabelsKeyboard($labelGroup, $data->id);

        $req_data = [
            'chat_id'                  => $this->chat_id,
            'text'                     => $data->data,
            'disable_web_page_preview' => true,
            'reply_markup'             => $inline_keyboard,
        ];

        if ($edit_message_id) {
            $req_data['message_id'] = $edit_message_id;
            return Request::editMessageText($req_data);
        } else {
            return Request::sendMessage($req_data);
        }
    }

    public function assignLabel()
    {
        $callback_data = new CallbackData($this->moderator, $this->callback_query_data);
        list($data_id, $label_id) = explode(':', $callback_data->data);

        $earlyAssignedLabel = AssignedLabel::findOne([
            'data_id'      => $data_id,
            'moderator_id' => $this->moderator->id,                    
        ]);

        if ($earlyAssignedLabel) {
            $req_data = [
                'callback_query_id' => $this->callback_query_id,
                'text'              => 'This data was labeled already',
                'show_alert'        => false,
                'cache_time'        => 0,
            ];
            Request::answerCallbackQuery($req_data);
            return $this->sendData($this->message_id);
        }

        $assignedLabel = new AssignedLabel;
        $assignedLabel->data_id      = $data_id;
        $assignedLabel->label_id     = $label_id;
        $assignedLabel->moderator_id = $this->moderator->id;
        $assignedLabel->created_at   = time();

        if (!$assignedLabel->save()) {
            // TODO: log error
        }

        return $this->sendData($this->message_id);
    }

    public function nextLabelGroup()
    {
        $callback_data = new CallbackData($this->moderator, $this->callback_query_data);
        list($data_id, $label_group_id) = explode(':', $callback_data->data);

        $nextLabelGroup = LabelGroup::findOne($label_group_id);
        $inline_keyboard = $this->generateLabelsKeyboard($nextLabelGroup, $data_id);

        $req_data = [
            'chat_id'      => $this->chat_id,
            'message_id'   => $this->message_id,
            'text'         => $this->message->getText(),
            'reply_markup' => $inline_keyboard,
        ];

        return Request::editMessageText($req_data);
    }


    private function generateLabelsKeyboard(LabelGroup $labelGroup, int $data_id) : InlineKeyboard
    {
        $keyboard = [];
        foreach ($labelGroup->getLabels()->all() as $label) {
            array_push($keyboard, $this->generateLabelKey($label, $data_id));
        }
        return new InlineKeyboard($keyboard);
    }


    private function generateLabelKey(Label $label, int $data_id) : array
    {
        $callback_data = new CallbackData($this->moderator);

        if ($label->next_label_group_id === null) {
            $callback_data->type = CallbackData::LABEL_ASSIGN;
            $callback_data->data = $data_id .':'. $label->id;    
        } else {
            $callback_data->type = CallbackData::NEXT_LABEL_GROUP;
            $callback_data->data = $data_id .':'. $label->next_label_group_id;   
        }

        return [
            'text' => $label->text,
            'callback_data' => $callback_data->toString()
        ];
    }

}
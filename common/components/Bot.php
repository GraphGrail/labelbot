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


class Bot extends yii\base\BaseObject
{
    public $cmd;
    public $chat_id;
    public $moderator_id;
    public $moderator;

    public function __construct($cmd, $config = [])
    {
        $this->cmd = $cmd;
        $this->chat_id = $cmd->getCallbackQuery()
                       ? $cmd->getCallbackQuery()->getMessage()->getChat()->getId()
                       : $cmd->getMessage()->getChat()->getId();

        $this->moderator = Moderator::findOne(['tg_id'=>$this->chat_id]);

        if ($this->moderator == null) {
            // TODO: need auth!!
        }

        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
        // ... initialization after configuration is applied

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

        $inline_keyboard = self::generateKeyboard($labelGroup, $data->id);

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
        // TODO: Refactoring !!
        $callback_query    = $this->cmd->getCallbackQuery();

        $callback_query_id = $callback_query->getId();
        $message_id        = $callback_query->getMessage()->getMessageId();

        $callback_data = new class{};
        list($callback_data->type, $callback_data->data_id, $callback_data->label_id, $callback_data->sign) = explode(':', $callback_query->getData());


        if (!hash_equals($callback_data->sign, crypt($callback_data->data_id . $callback_data->label_id, Yii::$app->params['telegram_bot_callback_secret_key']))) {
            //    return;
        }

        $earlyAssignedLabel = AssignedLabel::findOne([
            'data_id'      => $callback_data->data_id,
            'moderator_id' => $this->moderator->id,                    
        ]);

        if ($earlyAssignedLabel) {
            $data = [
                        'callback_query_id' => $callback_query_id,
                        'text'              => 'This data was labeled already',
                        'show_alert'        => false,
                        'cache_time'        => 0,
                    ];
            Request::answerCallbackQuery($data);
            return $this->sendData($message_id);
        }

        $assignedLabel = new AssignedLabel;
        $assignedLabel->data_id      = $callback_data->data_id;
        $assignedLabel->label_id     = $callback_data->label_id;
        $assignedLabel->moderator_id = $this->moderator->id;
        $assignedLabel->created_at   = time();

        if (!$assignedLabel->save()) {
            // TODO: log error
        }

        return $this->sendData($message_id);
    }

    public function nextLabelGroup()
    {
        // TODO: Refactoring !!
        $callback_query    = $this->cmd->getCallbackQuery();

        $callback_query_id = $callback_query->getId();
        $chat_id           = $callback_query->getMessage()->getChat()->getId();
        $message_id        = $callback_query->getMessage()->getMessageId();
        $text              = $callback_query->getMessage()->getText();
        $tg_id             = $callback_query->getFrom()->getId();

        $callback_data = new class{};
        list($callback_data->type, $callback_data->label_group_id, $callback_data->data_id) = explode(':', $callback_query->getData());

        $nextLabelGroup = LabelGroup::findOne($callback_data->label_group_id);
        $inline_keyboard = self::generateKeyboard($nextLabelGroup, $callback_data->data_id);

        $req_data = [
            'chat_id'      => $chat_id,
            'message_id'   => $message_id,
            'text'         => $text,
            'reply_markup' => $inline_keyboard,
        ];

        return Request::editMessageText($req_data);
    }


    public function getCallbackType() : string
    {
        $callback_query    = $this->cmd->getCallbackQuery();
        $callback_data     = explode(':', $callback_query->getData());
        $callback_type     = $callback_data[0];
        return $callback_type;
    }


    private static function generateKeyboard(LabelGroup $labelGroup, int $data_id) : InlineKeyboard
    {
        $keyboard = [];
        foreach ($labelGroup->getLabels()->all() as $label) {
            array_push($keyboard, self::generateKeyboardLabel($label, $data_id));
        }
        return new InlineKeyboard($keyboard);
    }


    private static function generateKeyboardLabel(Label $label, int $data_id) : array
    {
        if ($label->next_label_group_id === null) {
            $sign = crypt($data_id . $label->id, Yii::$app->params['telegram_bot_callback_secret_key']);
            $callback_data = [
                    'type'     => 'label_assign',
                    'data_id'  => $data_id,
                    'label_id' => $label->id,
                    'sign'     => $sign
                ];
            return [
                'text' => $label->text,
                'callback_data' => implode($callback_data, ':')
            ];            
        } else {
            $callback_data = [
                    'type'           => 'next_label_group',
                    'label_group_id' => $label->next_label_group_id,
                    'data_id'        => $data_id
                ];
            return [
                'text' => $label->text,
                'callback_data' => implode($callback_data, ':')
            ];             
        }
    }

}
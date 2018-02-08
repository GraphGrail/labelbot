<?php

namespace common\components;

use Yii;
use Longman\TelegramBot\Entities\InlineKeyboard;
use common\models\Label;
use common\models\Moderator;
use common\components\CallbackData;


class KeyboardGenerator extends yii\base\BaseObject
{
    public static function labelsKeyboard(Label $root_label, int $data_id, Moderator $moderator) : InlineKeyboard
    {
        $keyboard = [];
        $labels = [];

        $labels = Label::findAll(['parent_label_id' => $root_label->id]);

        foreach ($labels as $label) {
            array_push($keyboard, [self::labelKey($label, $data_id, $moderator)]);
        }
        return new InlineKeyboard(...$keyboard);
    }

    private static function labelKey(Label $label, int $data_id, Moderator $moderator) : array
    {
        $callback_data = new CallbackData($moderator);
        $callback_data->type = CallbackData::LABEL_KEY_PRESSED;
        $callback_data->data = $data_id .':'. $label->id;    

        return [
            'text' => $label->text,
            'callback_data' => $callback_data->toString()
        ];
    }
}
<?php

namespace common\components;

use Yii;
use Longman\TelegramBot\Entities\InlineKeyboard;
use common\models\Label;
use common\models\Moderator;
use common\components\CallbackData;


class LabelsKeyboard extends yii\base\BaseObject
{
    protected $root_label;
    protected $data_id;
    protected $moderator;
    

    public function __construct(Label $root_label, int $data_id, Moderator $moderator)
    {
        $this->root_label = $root_label;
        $this->data_id = $data_id;
        $this->moderator = $moderator;

        parent::__construct();
    }

    public function generate() : InlineKeyboard
    {
        $keyboard = [];
        $labels = Label::findAll(['parent_label_id' => $this->root_label->id]);

        foreach ($labels as $label) {
            array_push($keyboard, [$this->labelKey($label)]);
        }

        if ($this->root_label->parent_label_id) {
            array_push($keyboard, [self::backKey($this->root_label)]);
        }

        return new InlineKeyboard(...$keyboard);
    }

    private function labelKey(Label $label) : array
    {
        $callback_data = new CallbackData($this->moderator);
        $callback_data->type = CallbackData::LABEL_KEY_PRESSED;
        $callback_data->data = $this->data_id .':'. $label->id;

        if (!$label->children) {
            $label->text = '✅ ' . $label->text;
        }

        return [
            'text' => $label->text,
            'callback_data' => $callback_data->toString()
        ];
    }

    private function backKey(Label $label) : array
    {
        $callback_data = new CallbackData($this->moderator);
        $callback_data->type = CallbackData::BACK_KEY_PRESSED;
        $callback_data->data = $this->data_id .':'. $label->parent_label_id;    

        return [
            'text' => '👈🏻 Back',
            'callback_data' => $callback_data->toString()
        ];
    }
}
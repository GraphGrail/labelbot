<?php

namespace common\components;

use Yii;
use Longman\TelegramBot\Entities\InlineKeyboard;
use common\models\Label;
use common\models\DataLabel;
use common\models\Moderator;

/**
 * LabelsKeybords component
 * 
 * Generate InlineKeyboard with labels for Telegram
 */
class LabelsKeyboard extends yii\base\BaseObject
{
    protected $root_label;
    protected $data_label_id;
    protected $moderator;

    /**
     * Class constructor
     *
     * @param Label $root_label
     * @param DataLabel $data_label
     * @param Moderator $moderator
     */
    public function __construct(Label $root_label, DataLabel $data_label, Moderator $moderator)
    {
        $this->root_label = $root_label;
        $this->data_label_id = $data_label->id;
        $this->moderator = $moderator;

        parent::__construct();
    }

    /**
     * Returns telegram InlineKeyboard
     *
     * @return InlineKeyboard
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function generate() : InlineKeyboard
    {
        $keyboard = [];
        $labels = Label::findAll(['parent_label_id' => $this->root_label->id]);

/*        if ($this->root_label->parent_label_id === 0) {
            array_push($keyboard, [$this->nextKey()]);
        }*/

        foreach ($labels as $label) {
            array_push($keyboard, [$this->labelKey($label)]);
        }

        if ($this->root_label->parent_label_id) {
            array_push($keyboard, [$this->backKey()]);
        }

        return new InlineKeyboard(...$keyboard);
    }

    /**
     * Returns Label key
     *
     * @param Label $label
     * @return array
     */
    private function labelKey(Label $label) : array
    {
        $callback_data = new CallbackData($this->moderator);
        $callback_data->type = CallbackData::LABEL_KEY_PRESSED;
        $callback_data->data = $this->data_label_id .':'. $label->id;

        if (!$label->children) {
            $label->text = '✅ ' . $label->text;
        }

        return [
            'text' => $label->text,
            'callback_data' => $callback_data->toString()
        ];
    }

    /**
     * Returns Next key
     * 
     * @return array
     */
    private function nextKey() : array
    {
        $callback_data = new CallbackData($this->moderator);
        $callback_data->type = CallbackData::NEXT_KEY_PRESSED;
        $callback_data->data = $this->data_label_id .':'. 0;

        return [
            'text' => 'Next data 👉🏻',
            'callback_data' => $callback_data->toString()
        ];
    }

    /**
     * Returns Back key
     * 
     * @return array
     */
    private function backKey() : array
    {
        $callback_data = new CallbackData($this->moderator);
        $callback_data->type = CallbackData::BACK_KEY_PRESSED;
        $callback_data->data = $this->data_label_id .':'. $this->root_label->parent_label_id;

        return [
            'text' => '👈🏻 Back',
            'callback_data' => $callback_data->toString()
        ];
    }
}
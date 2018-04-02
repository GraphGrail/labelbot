<?php

namespace frontend\models;

use yii\base\Model;


class SendScoreWorkForm extends Model
{
    public $workers;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['workers', 'required'],
        ];
    }
}

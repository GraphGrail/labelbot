<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "assigned_label".
 *
 * @property int $id
 * @property int $data_id
 * @property int $label_id
 * @property int $moderator_id
 * @property int $created_at
 */
class AssignedLabel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'assigned_label';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data_id', 'label_id', 'moderator_id', 'created_at'], 'required'],
            [['data_id', 'label_id', 'moderator_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data_id' => 'Data ID',
            'label_id' => 'Label ID',
            'moderator_id' => 'Moderator ID',
            'created_at' => 'Created At',
        ];
    }

    public function getData()
    {
        return $this->hasOne(Data::className(), ['id' => 'data_id']);
    }

    public function getLabel()
    {
        return $this->hasOne(Label::className(), ['id' => 'label_id']);
    }

    public function getModerator()
    {
        return $this->hasOne(Moderator::className(), ['id' => 'moderator_id']);
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "label".
 *
 * @property int $id
 * @property int $label_group_id
 * @property string $text
 * @property int $next_label_group_id
 * @property int $ordering
 * @property int $created_at
 * @property int $updated_at
 */
class Label extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'label';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label_group_id', 'next_label_group_id', 'ordering', 'created_at', 'updated_at'], 'integer'],
            [['text'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label_group_id' => 'Label Group ID',
            'text' => 'Text',
            'next_label_group_id' => 'Next Label Group ID',
            'ordering' => 'Ordering',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getLabelGroup()
    {
        return $this->hasOne(LabelGroup::className(), ['id' => 'label_group_id']);
    }
    
}

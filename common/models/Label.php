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
            [['parent_label_id', 'label_group_id', 'ordering', 'created_at', 'updated_at'], 'integer'],
            [['text'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_label_id' => 'Parent Label ID',
            'text' => 'Text',
            'label_group_id' => 'Label Group ID',
            'ordering' => 'Ordering',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getLabelGroup()
    {
        return $this->hasOne(LabelGroup::className(), ['id' => 'label_group_id']);
    }

    public function getChildren() {
        return self::findAll(['parent_label_id' => $this->id]);
    }
    
}

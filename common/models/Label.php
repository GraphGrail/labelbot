<?php

namespace common\models;


/**
 * This is the model class for table "label".
 *
 * @property int $id
 * @property int $label_group_id
 * @property int $parent_label_id
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
            [['parent_label_id', 'label_group_id', 'ordering'], 'integer'],
            [['text'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::class,
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLabelGroup()
    {
        return $this->hasOne(LabelGroup::class, ['id' => 'label_group_id']);
    }

    /**
     * @return Label[]
     */
    public function getChildren() {
        return self::findAll(['parent_label_id' => $this->id]);
    }

    /**
     * @return Label|null
     */
    public function getParent(): ?Label
    {
        if (!$this->parent_label_id) {
            return null;
        }
        return self::findOne($this->parent_label_id);
    }

    /**
     * @return array
     */
    public function buildPath()
    {
        $res = [[$this->text]];
        if ($parent = $this->getParent()) {
            $res[] = array_reverse($parent->buildPath());
        }
        return array_reverse(array_merge(...$res));
    }
}

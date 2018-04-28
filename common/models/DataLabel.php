<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "data_label".
 *
 * @property int $id
 * @property int $data_id
 * @property int $label_id
 * @property int $moderator_id
 * @property int $created_at
 * @property int $updated_at
 */
class DataLabel extends \yii\db\ActiveRecord
{
    /**
     * Statuses
     */
    const STATUS_NEW      = 10;
    const STATUS_READY    = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'data_label';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'work_item_id', 'data_id'], 'required'],
            [['task_id', 'work_item_id', 'data_id', 'label_id', 'moderator_id', 'created_at', 'updated_at'], 'integer'],
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
            'data_id' => 'Data ID',
            'task_id' => 'Task ID',
            'work_item_id' => 'Work item ID',
            'label_id' => 'Label ID',
            'moderator_id' => 'Moderator ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At'
        ];
    }

    /**
     * Returns related Task model
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Returns related WorkItem model
     * @return \yii\db\ActiveQuery
     */
    public function getWorkItem()
    {
        return $this->hasOne(WorkItem::class, ['id' => 'work_item_id']);
    }

    /**
     * Returns related Data model
     * @return \yii\db\ActiveQuery
     */
    public function getData()
    {
        return $this->hasOne(Data::class, ['id' => 'data_id']);
    }

    /**
     * Returns related Label model
     * @return \yii\db\ActiveQuery
     */
    public function getLabel()
    {
        return $this->hasOne(Label::class, ['id' => 'label_id']);
    }

    /**
     * Returns related Moderator model
     * @return \yii\db\ActiveQuery
     */
    public function getModerator()
    {
        return $this->hasOne(Moderator::class, ['id' => 'moderator_id']);
    }



}

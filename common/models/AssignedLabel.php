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
 * @property int $updated_at
 */
class AssignedLabel extends \yii\db\ActiveRecord
{
    /**
     * Statuses
     */
    const STATUS_IN_HAND  = 1;
    const STATUS_READY    = 2;
    const STATUS_APPROVED = 3;
    const STATUS_DECLINED = 4;

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
            [['data_id', 'moderator_id'], 'required'],
            [['data_id', 'label_id', 'moderator_id', 'created_at', 'updated_at'], 'integer'],
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
            'data_id' => 'Data ID',
            'task_id' => 'Task ID',
            'label_id' => 'Label ID',
            'moderator_id' => 'Moderator ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At'
        ];
    }

    /**
     * Returns related Data model
     * @return common\models\Task
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    /**
     * Returns related Data model
     * @return common\models\Data
     */
    public function getData()
    {
        return $this->hasOne(Data::className(), ['id' => 'data_id']);
    }

    /**
     * Returns related Label model
     * @return common\models\Label
     */
    public function getLabel()
    {
        return $this->hasOne(Label::className(), ['id' => 'label_id']);
    }

    /**
     * Returns related Modertor model
     * @return common\models\Moderator
     */
    public function getModerator()
    {
        return $this->hasOne(Moderator::className(), ['id' => 'moderator_id']);
    }
}

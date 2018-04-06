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
    const STATUS_NEW      = 10;
    const STATUS_IN_HAND  = 20;
    const STATUS_SKIPPED  = 30;
    const STATUS_READY    = 40;
    const STATUS_APPROVED = 50;
    const STATUS_DECLINED = 60;

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
            [['task_id', 'data_id'], 'required'],
            [['task_id', 'data_id', 'label_id', 'moderator_id', 'created_at', 'updated_at'], 'integer'],
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
     * @return \common\models\Task
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    /**
     * Returns related Data model
     * @return \common\models\Data
     */
    public function getData()
    {
        return $this->hasOne(Data::className(), ['id' => 'data_id']);
    }

    /**
     * Returns related Label model
     * @return \common\models\Label
     */
    public function getLabel()
    {
        return $this->hasOne(Label::className(), ['id' => 'label_id']);
    }

    /**
     * Returns related Modertor model
     * @return \common\models\Moderator
     */
    public function getModerator()
    {
        return $this->hasOne(Moderator::className(), ['id' => 'moderator_id']);
    }


    /**
     * Deletes AssignedLabels that weren't assigned in $seconds 
     * 
     * @var $seconds
     * @return int
     */
    public static function handleSkippedLabels($seconds=180)
    {
        $expired_time = time() - $seconds;
        return AssignedLabel::updateAll(
            ['status' => AssignedLabel::STATUS_SKIPPED],
            ['and', 
                ['status' => AssignedLabel::STATUS_IN_HAND],
                ['<', 'created_at', $expired_time]
            ]
        );
    }

    
    public static function updateStatuses(int $task_id, int $from_status, int $to_status, int $moderator_id, int $limit) : int
    {
        $updates = Yii::$app->db->createCommand('
            UPDATE assigned_label 
                SET status=:new_status
                WHERE task_id=:task_id
                    AND moderator_id=:moderator_id
                    AND status=:old_status
                ORDER BY id ASC 
                LIMIT ' . $limit)
            ->bindParam(':new_status',   $to_status)
            ->bindParam(':task_id',      $task_id)
            ->bindParam(':moderator_id', $moderator_id)
            ->bindParam(':old_status',   $from_status)
            ->execute();

        return $updates;       
    }
}

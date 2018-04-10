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
    const STATUS_APPROVED = 30;
    const STATUS_DECLINED = 40;

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
     * @return \common\models\Task
     */
    public function getTask()
    {
        return $this->hasOne(Task::className(), ['id' => 'task_id']);
    }

    /**
     * Returns related WorkItem model
     * @return \common\models\Task
     */
    public function getWorkItem()
    {
        return $this->hasOne(WorkItem::className(), ['id' => 'work_item_id']);
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


    
/*    public static function updateStatuses(int $task_id, int $from_status, int $to_status, int $moderator_id, int $limit) : int
    {
        $updates = Yii::$app->db->createCommand('
            UPDATE assigned_label 
                SET status=:new_status
                WHERE task_id=:task_id
                    AND moderator_id=:moderator_id
                    AND status=:old_status
                ORDER BY updated_at ASC 
                LIMIT ' . $limit)
            ->bindParam(':new_status',   $to_status)
            ->bindParam(':task_id',      $task_id)
            ->bindParam(':moderator_id', $moderator_id)
            ->bindParam(':old_status',   $from_status)
            ->execute();

        return $updates;       
    }*/

/*    public static function copyDeclinedToNew(int $task_id, int $moderator_id, int $limit)
    {
        $declinedLabels = (new \yii\db\Query)
            ->select(['data_id'])
            ->from(AssignedLabel::tableName())
            ->where([
                'task_id' => $task_id,
                'moderator_id' => $moderator_id,
                'status' => AssignedLabel::STATUS_DECLINED
            ])
            ->orderBy('updated_at ASC') // !
            ->limit($limit)
            ->all();

        $timestamp = time();
        $dataToInsert = [];
        foreach (array_reverse($declinedLabels) as $declinedLabel) {
            $dataToInsert []= [$task_id, $declinedLabel['data_id'], AssignedLabel::STATUS_NEW, $timestamp, $timestamp];
        }
        Yii::$app->db->createCommand()->batchInsert(
            AssignedLabel::tableName(), 
            ['task_id', 'data_id', 'status', 'created_at', 'updated_at'], 
            $dataToInsert
        )->execute();
              
    }*/
}

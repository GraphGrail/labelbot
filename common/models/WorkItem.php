<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;


/**
 * Class WorkItem
 * @package common\models
 * @property integer id
 * @property integer task_id
 * @property integer moderator_id
 * @property integer items
 * @property integer created_at
 * @property integer updated_at
 */
class WorkItem extends ActiveRecord
{
    const STATUS_FREE     = 10;
    const STATUS_IN_HAND  = 20;
    const STATUS_READY    = 40;
    const STATUS_APPROVED = 50;
    const STATUS_DECLINED = 60;


    public static function tableName()
    {
        return 'work_item';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function lock(): bool
    {
        return Lock::create($this);
    }

    public function free(): bool
    {
        Lock::free($this);
        return true;
    }

    public function getDataLabels()
    {
        return $this->hasMany(DataLabel::class, ['work_item_id' => 'id']);
    }


    public function getNewDataLabel() : ?DataLabel
    {
        return DataLabel::findOne([
           'work_item_id' => $this->id,
           'status' => DataLabel::STATUS_NEW
        ]);
    }


    public function approve()
    {
        $this->status = WorkItem::STATUS_APPROVED;
        return $this->save();
    }


    public function decline()
    {
        $declinedLabels = DataLabel::find()
            ->where(['work_item_id'=>$this->id])
            ->all()
            ->asArray();

        $timestamp = time();
        $dataToInsert = [];

        foreach ($declinedLabels as $declinedLabel) {
            $dataToInsert []= [$task_id, $declinedLabel['data_id'], $this->id, DataLabel::STATUS_NEW, $timestamp, $timestamp];
        }
        Yii::$app->db->createCommand()->batchInsert(
            DataLabel::tableName(),
            ['task_id', 'data_id', 'work_item_id', 'status', 'created_at', 'updated_at'],
            $dataToInsert
        )->execute();

        $this->status = WorkItem::STATUS_DECLINED;
        return $this->save();
    }


    public static function updateStatuses(int $task_id, int $from_status, int $to_status, int $moderator_id, int $limit) : int
    {
        $updates = Yii::$app->db->createCommand('
            UPDATE work_item 
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
    }


}
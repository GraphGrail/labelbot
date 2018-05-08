<?php

namespace common\models;

use common\models\behavior\LockEntityBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Class WorkItem
 *
 * @package common\models
 * @property integer id
 * @property integer task_id
 * @property integer moderator_id
 * @property integer moderator_address
 * @property integer items
 * @property integer status
 * @property integer created_at
 * @property integer updated_at
 */
class WorkItem extends ActiveRecord
{
    const STATUS_FREE = 10;
    const STATUS_IN_HAND = 20;
    const STATUS_READY = 40;
    const STATUS_APPROVED = 50;
    const STATUS_DECLINED = 60;
    const STATUS_NOT_FINISHED = 70;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'work_item';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => LockEntityBehavior::class,
                'entity' => $this
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDataLabels()
    {
        return $this->hasMany(DataLabel::class, ['work_item_id' => 'id']);
    }

    /**
     * Returns related Moderator model
     * @return \yii\db\ActiveQuery
     */
    public function getModerator()
    {
        return $this->hasOne(Moderator::class, ['id' => 'moderator_id']);
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
     * @return DataLabel|null
     */
    public function getNewDataLabel(): ?DataLabel
    {
        return DataLabel::findOne([
            'work_item_id' => $this->id,
            'status' => DataLabel::STATUS_NEW
        ]);
    }

    /**
     * @return bool
     */
    public function approve()
    {
        $this->status = WorkItem::STATUS_APPROVED;
        return $this->save();
    }

    /**
     * @return bool
     */
    public function decline()
    {
        try {
            return $this->reset(WorkItem::STATUS_DECLINED);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function notFinished()
    {
        try {
            return $this->reset(WorkItem::STATUS_NOT_FINISHED);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $status
     * @return bool
     * @throws \yii\db\Exception
     */
    protected function reset($status)
    {
        $newWorkItem = new WorkItem;
        $newWorkItem->task_id = $this->task_id;
        $newWorkItem->items = $this->items;
        $newWorkItem->status = WorkItem::STATUS_FREE;
        $newWorkItem->save();

        $timestamp = time();
        $dataToInsert = [];

        foreach ($this->dataLabels as $declinedLabel) {
            $dataToInsert [] = [
                $newWorkItem->id,
                $declinedLabel['data_id'],
                DataLabel::STATUS_NEW,
                $timestamp,
                $timestamp
            ];
        }

        Yii::$app->db->createCommand()->batchInsert(
            DataLabel::tableName(),
            ['work_item_id', 'data_id', 'status', 'created_at', 'updated_at'],
            $dataToInsert
        )->execute();

        $this->status = $status;
        return $this->save();
    }

    /**
     * @return DataLabel
     */
    public function getRandomDataLabel(): DataLabel
    {
        $dataLabels = $this->dataLabels;
        return $dataLabels[array_rand($dataLabels)];
    }

}

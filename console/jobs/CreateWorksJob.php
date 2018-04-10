<?php

namespace console\jobs;

use common\models\Task;
use common\models\Data;
use common\models\DataLabel;
use common\models\WorkItem;
use Yii;


/**
 * Class CreateWorkItemsJob.
 */
class CreateWorksJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $task_id;
    public $task;

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute($queue)
    {
        /** @var Task $task */
        $this->task = Task::findOne($this->task_id);

        $count = (new \yii\db\Query)
            ->select(['id'])
            ->from(Data::tableName())
            ->where(['dataset_id' => $this->task->dataset_id])
            ->count();

        $workItemsSize = $this->task->work_item_size;
        $worksCount = bcdiv($count, $this->task->work_item_size);
        $extraCount = bcmod($count, $this->task->work_item_size);


        $ratio = bcdiv($extraCount, $worksCount, 0);
        if ($ratio > 0) {
            $workItemsSize += $ratio;
        }

        $rest = $extraCount - ($ratio * $worksCount);

        $itemCountFrom = 0;

        while($worksCount) {
            $workItem = new WorkItem();
            $workItem->task_id = $this->task->id;

            $itemCount = $workItemsSize;
            if ($rest > 0) {
                $itemCount++;
                $rest--;
            }
            $workItem->items = $itemCount;
            $workItem->save();

            $this->createDataLabels($workItem->id, $itemCountFrom, $itemCount);

            $itemCountFrom += $itemCount;
            $worksCount--;
        }

        $this->task->status = Task::STATUS_CONTRACT_NEW_NEED_TOKENS;
        if (!$this->task->save()) {
            throw new \Exception("Can't save Task");
        }

    }


    public function createDataLabels(int $work_item_id, int $from, int $num)
    {
        $data = (new \yii\db\Query)
            ->select(['id'])
            ->from(Data::tableName())
            ->where(['dataset_id'=>$this->task->dataset_id])
            ->orderBy('id')
            ->limit($num)
            ->offset($from);

        $timestamp = time();
        foreach ($data->batch(100) as $ids) {
            $dataToInsert = [];
            foreach ($ids as $id) {
                $dataToInsert []= [$this->task->id, $work_item_id, $id['id'], DataLabel::STATUS_NEW, $timestamp, $timestamp];
            }
            Yii::$app->db->createCommand()->batchInsert(
                DataLabel::tableName(), 
                ['task_id', 'work_item_id', 'data_id', 'status', 'created_at', 'updated_at'], 
                $dataToInsert
            )->execute();
        }
    }

}

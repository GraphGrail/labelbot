<?php

namespace console\jobs;

use common\models\Data;
use common\models\Task;
use common\models\WorkItem;

/**
 * Class ParseDatasetJob.
 */
class CreateWorkItemsJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $task_id;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        /** @var Task $task */
        $task = Task::findOne($this->task_id);

        $count = (new \yii\db\Query)
            ->select(['id'])
            ->from(Data::tableName())
            ->where(['dataset_id' => $task->dataset_id])
            ->count();

        $workItemsSize = $task->work_item_size;
        $worksCount = bcdiv($count, $task->work_item_size);
        $extraCount = bcmod($count, $task->work_item_size);


        $ratio = bcdiv($extraCount, $worksCount, 0);
        if ($ratio > 0) {
            $workItemsSize += $ratio;
        }

        $rest = $extraCount - ($ratio * $worksCount);


        while($worksCount) {
            $worksCount--;

            $workItem = new WorkItem();
            $workItem->task_id = $this->task_id;

            $itemCount = $workItemsSize;
            if ($rest > 0) {
                $itemCount++;
                $rest--;
            }
            $workItem->items = $itemCount;


            $workItem->save();
        }

        $task->status = Task::STATUS_CONTRACT_NEW_NEED_TOKENS;
        if (!$task->save()) {
            throw new \Exception("Can't save Task");
        }

    }
}

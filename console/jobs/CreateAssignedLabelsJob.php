<?php

namespace console\jobs;

use common\models\Task;
use common\models\Data;
use common\models\AssignedLabel;
use Yii;

/**
 * Class ParseDatasetJob.
 */
class CreateAssignedLabelsJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $task_id;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
    	$task = Task::findOne($this->task_id);

        $data = (new \yii\db\Query)
            ->select(['id'])
            ->from(Data::tableName())
            ->where(['dataset_id'=>$task->dataset_id])
            ->orderBy('id');

        foreach ($data->batch(100) as $ids) {
            $timestamp = time();
            $dataToInsert = [];
            foreach ($ids as $id) {
                $dataToInsert []= [$task->id, $id['id'], AssignedLabel::STATUS_NEW, $timestamp, $timestamp];
            }
            Yii::$app->db->createCommand()->batchInsert(
                AssignedLabel::tableName(), 
                ['task_id', 'data_id', 'status', 'created_at', 'updated_at'], 
                $dataToInsert
            )->execute();
        }

        $task->status = Task::STATUS_CONTRACT_NEW_NEED_TOKENS;
        if (!$task->save()) {
            throw new \Exception("Can't save Task");
        }

    }
}

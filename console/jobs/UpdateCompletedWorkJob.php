<?php

namespace console\jobs;

use common\models\BlockchainCallback;
use common\models\Task;
use common\models\WorkItem;
use common\components\EthereumGateway;

/**
 * Class UpdateCompletedWorkJob.
 */
class UpdateCompletedWorkJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $taskId;

    /** @var Task */
    protected $task;

    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();
        $this->task = Task::findOne($this->taskId);
        if ($this->task === null) {
            throw new \Exception('Cant find Task record');
        }
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function execute($queue)
    {
        //free task for future jobs
        $this->freeTask();

        $payload = [];

        $currentWorks = (new \yii\db\Query)
            ->select(['moderator_address', 'COUNT(moderator_address) AS count'])
            ->from(WorkItem::tableName())
            ->where(['task_id'=>$this->task->id])
            ->andWhere(['in', 'status', [WorkItem::STATUS_READY, WorkItem::STATUS_APPROVED, WorkItem::STATUS_DECLINED]])
            ->groupBy(['moderator_address'])
            ->all();

        foreach ($currentWorks as $work) {
            $payload[$work['moderator_address']] = (int) $work['count'];
        }

        $blockchain = new EthereumGateway;
        $callback_id = $blockchain->updateCompletedWork($this->task->contractAddress(), $payload);
        // TODO: handle error
        $callback = new BlockchainCallback();
        $callback->type = BlockchainCallback::UPDATE_COMPLETED_WORK;
        $callback->callback_id = $callback_id;
        $callback->params = json_encode(['task_id' => $this->task->id]);
        
        if (!$callback->save()) {
            throw new \Exception("Can't save Callback after updateCompletedWork() was called");
        }
    }

    protected function freeTask() {
        $this->task->delivering_job_id = 0;
        return $this->task->save(false, ['delivering_job_id']);
    }
}

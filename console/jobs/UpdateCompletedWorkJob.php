<?php

namespace console\jobs;
use common\models\Task;
use common\models\AssignedLabel;
use common\models\BlockchainCallback;
use common\components\EthereumGateway;
use Yii;

/**
 * Class UpdateCompletedWorkJob.
 */
class UpdateCompletedWorkJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $taskId;

    /** @var Task */
    protected $task;

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
     */
    public function execute($queue)
    {
        //free task for future jobs
        $this->freeTask();

        $payload = [];

        $currentWorks = (new \yii\db\Query)
            ->select(['moderator_id', 'moderator.eth_addr', 'COUNT(moderator_id) AS count'])
            ->from(AssignedLabel::tableName())
            ->join('JOIN', 'moderator', 'moderator.id = moderator_id')
            ->where(['task_id'=>$this->task->id])
            ->andWhere(['in', 'status', [AssignedLabel::STATUS_READY, AssignedLabel::STATUS_APPROVED, AssignedLabel::STATUS_DECLINED]])
            ->groupBy(['moderator_id'])
            ->all();

        foreach ($currentWorks as $work) {
            $readyWorkItems = (int) $work['count'] / $this->task->work_item_size;
            // We don't get not completed workItems
            if ($readyWorkItems === 0) continue;

            $payload[$work['eth_addr']] = $readyWorkItems;
        }

        if ($payload === []) return;

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

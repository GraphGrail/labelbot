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

        $currentWorks = Yii::$app->db->createCommand("
            SELECT `assigned_label`.moderator_id, `moderator`.eth_addr, COUNT(`assigned_label`.moderator_id) AS count
            FROM `assigned_label` 
            LEFT JOIN `moderator` on `assigned_label`.moderator_id = `moderator`.id 
            WHERE (`assigned_label`.status IN (".AssignedLabel::STATUS_READY.", ".AssignedLabel::STATUS_APPROVED.", ".AssignedLabel::STATUS_DECLINED."))
            GROUP BY `assigned_label`.moderator_id
        ")->queryAll();

        foreach ($currentWorks as $work) {
            $readyWorkItems = (int) $work['count'] / $this->task->work_item_size;
            // We don't get not completed workItems
            if ($readyWorkItems === 0) continue;

            $payload[$work['eth_addr']] = $readyWorkItems;
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

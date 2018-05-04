<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace console\jobs;

use common\components\EthereumGateway;
use common\models\BlockchainCallback;
use common\models\Task;
use common\models\task\QueueTask;
use yii\base\BaseObject;
use yii\log\Logger;
use yii\queue\JobInterface;
use yii\queue\Queue;

class SynchronizeTaskStatusJob extends BaseObject implements JobInterface
{
    public const DEPLOYMENT_TIME_LIMIT = 10;

    public $taskId;

    /** @var Task */
    protected $task;

    /**
     * @throws \RuntimeException
     */
    public function init()
    {
        parent::init();
        $this->task = Task::findOne($this->taskId);
        if ($this->task === null) {
            throw new \RuntimeException('Cant find Task record');
        }
    }

    /**
     * @param Queue $queue which pushed and is handling the job
     */
    public function execute($queue)
    {
        QueueTask::freeTask($this->task, $this);

        if ($this->task->isContractDeploying() && $this->isDeployingCallbackFail()) {
            $this->task->setContractDeploymentError();
            return;
        }
        $blockchain = new EthereumGateway;
        try {
            $ethStatus = $blockchain->contractStatus($this->task->contractAddress());
            switch ($ethStatus->state) {
                case 'ACTIVE':
                    if ($this->task->isContractNew()) {
                        $this->markAsActive();
                    }
                    break;
                case 'FINALIZED':
                    $this->markAsFinalized();
                    break;
                case 'FORCE_FINALIZING':
                    $this->markAsFinalized();
                    break;
                default:
                    break;
            }
        } catch (\Exception $e) {
            \Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
        }
    }

    protected function markAsFinalized()
    {
        if ($this->task->isFinalized()) {
            return;
        }
        $this->task->setFinalized();
    }

    protected function markAsForceFinalizing()
    {
        if ($this->task->isForceFinalizing()) {
            return;
        }
        $this->task->setForceFinalizing();
    }

    private function markAsActive()
    {
        if ($this->task->isContractActive()) {
            return;
        }
        $this->task->setContractActive();
    }

    private function isDeployingCallbackFail(): bool
    {
        $condition = sprintf("LOCATE('\"task_id\":%s', params)", $this->taskId);
        /** @var BlockchainCallback $cb */
        if (!$cb = BlockchainCallback::find()
            ->where(['type' => BlockchainCallback::DEPLOY_CONTRACT])
            ->andWhere($condition)
            ->one()) {
            return true;
        }
        if ($cb->received) {
            return false;
        }
        $passTime = time() - $cb->created_at;
        $minutes = $passTime / 60;

        return $minutes >= self::DEPLOYMENT_TIME_LIMIT;
    }
}
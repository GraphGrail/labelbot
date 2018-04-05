<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace console\jobs;


use common\components\EthereumGateway;
use common\models\Task;
use yii\base\BaseObject;
use yii\log\Logger;
use yii\queue\JobInterface;
use yii\queue\Queue;

class SynchronizeTaskStatusJob extends BaseObject implements JobInterface
{

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
        $blockchain = new EthereumGateway;
        try {
            $ethStatus = $blockchain->contractStatus($this->task->contractAddress());
            switch ($ethStatus->state) {
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
}
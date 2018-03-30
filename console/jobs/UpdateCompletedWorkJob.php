<?php

namespace console\jobs;
use common\models\Task;

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
    }


    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        //free task for future jobs
        $this->freeTask();
    }

    protected function freeTask() {
        $this->task->delivering_job_id = 0;
        return $this->task->save(false, ['delivering_job_id']);
    }
}

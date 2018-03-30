<?php

namespace console\jobs;

/**
 * Class UpdateCompletedWorkJob.
 */
class UpdateCompletedWorkJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $taskId;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {


    }
}

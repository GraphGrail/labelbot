<?php

namespace console\controllers;

use common\models\Task;
use common\models\task\QueueTask;
use console\jobs\SynchronizeTaskStatusJob;
use console\jobs\UpdateCompletedWorkJob;
use yii\console\ExitCode;
use yii\log\Logger;

class BlockchainController extends \yii\console\Controller
{

    public $taskId;

    public function options($actionID)
    {
        return ['taskId'];
    }

    public function actionUpdateCompletedWork()
    {
        try {
            $finder = Task::find()
                ->contractActive()
                ->notInDeliveringQueue();

            $this->taskId && $finder->andWhere(['id' => $this->taskId]);

            /** @var Task[] $tasks */
            $tasks = $finder->all();

            foreach ($tasks as $task) {
                try {
                    $this->addUpdateWorkJobToQueue($task);
                } catch (\Exception $e) {
                    \Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return ExitCode::OK;
    }

    protected function addUpdateWorkJobToQueue(Task $task)
    {
        $id = \Yii::$app->queue->push(new UpdateCompletedWorkJob([
            'taskId' => $task->id,
        ]));

        $task->delivering_job_id = $id;
        return $task->save(false, ['delivering_job_id']);
    }

    public function actionSyncStatus()
    {
        $tasks = Task::find()
            ->contractActive()
            ->all()
        ;

        foreach ($tasks as $task) {
            try {
                $this->addSyncStatusJobToQueue($task);
            } catch (\Exception $e) {
                \Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
            }
        }
    }

    protected function addSyncStatusJobToQueue(Task $task)
    {
        $job = new SynchronizeTaskStatusJob([
            'taskId' => $task->id,
        ]);
        if (QueueTask::hasTask($task, $job)) {
            return;
        }
        QueueTask::addTask($task, $job);


        \Yii::$app->queue->push($job);

    }
}

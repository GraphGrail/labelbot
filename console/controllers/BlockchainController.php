<?php

namespace console\controllers;

use common\models\Task;
use console\jobs\UpdateCompletedWorkJob;
use yii\console\ExitCode;
use yii\log\Logger;

class BlockchainController extends \yii\console\Controller
{

    public function actionUpdateCompletedWork()
    {
        try {
            /** @var Task[] $tasks */
            $tasks = Task::find()
                ->active()
                ->undeleted()
                ->all();

            foreach ($tasks as $task) {
                try {
                    $this->addToQueue($task);
                } catch (\Exception $e) {
                    \Yii::getLogger()->log($e->getMessage(), Logger::LEVEL_ERROR);
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return ExitCode::OK;
    }

    protected function addToQueue(Task $task)
    {
        \Yii::$app->queue->push(new UpdateCompletedWorkJob([
            'taskId' => $task->id,
        ]));
    }
}

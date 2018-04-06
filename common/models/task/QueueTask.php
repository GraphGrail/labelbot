<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models\task;


use common\models\ActiveRecord;
use common\models\Task;

/**
 * Class Queue
 * @package common\models\task
 * @property integer task_id
 * @property string job
 */
class QueueTask extends ActiveRecord
{
    public static function tableName()
    {
        return 'queue_task';
    }

    public static function hasTask($task, $job = null)
    {
        $taskId = $task;
        if ($task instanceof Task) {
            $taskId = $task->id;
        }
        $condition = [
            'task_id' => $taskId,
        ];
        $job && $condition['job'] = \is_object($job) ? \get_class($job) : $job;

        return self::find()->where($condition)->count();
    }

    public static function addTask(Task $task, object $job)
    {
        $model = new static();
        $model->task_id = $task->id;
        $model->job = \get_class($job);
        $model->save();
    }

    public static function freeTask(Task $task, object $job)
    {
        $condition = [
            'task_id' => $task->id,
            'job' => \get_class($job)
        ];
        if (!$model = self::find()->where($condition)->one()) {
            return;
        }
        $model->delete();
    }
}
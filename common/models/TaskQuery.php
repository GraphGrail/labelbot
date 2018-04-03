<?php

namespace common\models;


use common\models\query\UserEntityQuery;

/**
 * This is the ActiveQuery class for [[Task]].
 *
 * @see Task
 */
class TaskQuery extends UserEntityQuery
{

    public function active()
    {
        return $this->andWhere('[[status]]='. Task::STATUS_CONTRACT_ACTIVE);
    }

    public function contractActive()
    {
        return $this->andWhere(['in', 'status', [
            Task::STATUS_CONTRACT_ACTIVE,
            Task::STATUS_CONTRACT_ACTIVE_NEED_TOKENS,
            Task::STATUS_CONTRACT_ACTIVE_WAITING_PAUSE,
            Task::STATUS_CONTRACT_ACTIVE_PAUSED
        ]]);
    }

    /**
     * @inheritdoc
     * @return Task[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Task|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notInDeliveringQueue()
    {
        return $this->andWhere('[[delivering_job_id]]=0');
    }
}

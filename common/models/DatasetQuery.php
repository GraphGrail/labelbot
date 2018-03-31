<?php

namespace common\models;


use common\models\query\UserEntityQuery;

/**
 * This is the ActiveQuery class for [[Dataset]].
 *
 * @see Task
 */
class DatasetQuery extends UserEntityQuery
{
    public function ready()
    {
        return $this->andWhere('[[status]]='. Dataset::STATUS_READY);
    }
}

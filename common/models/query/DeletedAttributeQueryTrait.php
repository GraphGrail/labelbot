<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models\query;

use yii\db\ActiveQuery;

trait DeletedAttributeQueryTrait
{
    /**
     * @return ActiveQuery
     */
    public function undeleted()
    {
        return $this->andWhere('[[deleted]]=0');
    }

    /**
     * @return ActiveQuery
     */
    public function deleted()
    {
        return $this->andWhere('[[deleted]]=1');
    }
}
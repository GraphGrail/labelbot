<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models\behavior;


use yii\base\Behavior;
use yii\db\ActiveQuery;

/**
 * Class DeletedAttributeBehavior
 * @package common\models\behavior
 * @property ActiveQuery $owner
 */
class DeletedAttributeQueryBehavior extends Behavior
{
    /**
     * @param \yii\base\Component|ActiveQuery $owner
     */
    public function attach($owner)
    {
        parent::attach($owner);

        $modelClass = $owner->modelClass;
        $tableName = $modelClass::tableName();
        $owner->andOnCondition([$tableName.'.deleted' => 0]);
    }
}
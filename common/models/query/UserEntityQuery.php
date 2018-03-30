<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models\query;


use common\models\User;
use yii\db\ActiveQuery;

class UserEntityQuery extends ActiveQuery
{
    use DeletedAttributeQueryTrait;

    public function ownedByUser(User $user = null)
    {
        return $this->andWhere(['user_id' => $user ? $user->id : \Yii::$app->user->identity->id]);
    }
}
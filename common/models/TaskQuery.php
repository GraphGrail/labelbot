<?php

namespace common\models;


use common\models\behavior\DeletedAttributeQueryBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the ActiveQuery class for [[Task]].
 *
 * @see Task
 */
class TaskQuery extends \yii\db\ActiveQuery
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'deletedAttribute' => [
                'class' => DeletedAttributeQueryBehavior::class,
            ],
        ]);
    }

    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

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
}

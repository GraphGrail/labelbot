<?php

namespace common\models;
use common\models\behavior\DeletedAttributeQueryBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the ActiveQuery class for [[LabelGroup]].
 *
 * @see Task
 */
class LabelGroupQuery extends \yii\db\ActiveQuery
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'deletedAttribute' => [
                'class' => DeletedAttributeQueryBehavior::class,
            ],
        ]);
    }
}

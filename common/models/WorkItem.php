<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models;

use yii\behaviors\TimestampBehavior;


/**
 * Class WorkItem
 * @package common\models
 * @property integer id
 * @property integer task_id
 * @property integer moderator_id
 * @property integer items
 * @property integer created_at
 * @property integer updated_at
 */
class WorkItem extends ActiveRecord
{
    public static function tableName()
    {
        return 'work_item';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function init()
    {
        parent::init();
        $this->updated_at = time();
        $this->created_at = time();
    }


    public function getData()
    {
        //todo
        return AssignedLabel::find()
            ->where(['task_id'=>$this->task_id])
            ->andWhere(['in', 'status', [AssignedLabel::STATUS_NEW, AssignedLabel::STATUS_SKIPPED]])
            ->orderBy('updated_at')
            ->one();
    }

    public function lock(): bool
    {
        return Lock::create($this);
    }

    public function free(): bool
    {
        Lock::free($this);
        return true;
    }
}
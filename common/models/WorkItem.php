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

    }

    public function lock(): bool
    {

    }

    public function free(): bool
    {

    }
}
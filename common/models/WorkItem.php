<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models;


/**
 * Class WorkItem
 * @package common\models
 * @property integer id
 * @property integer task_id
 * @property integer moderator_id
 */
class WorkItem extends ActiveRecord
{
    public static function tableName()
    {
        return 'work_item';
    }


    public function getData()
    {

    }
}
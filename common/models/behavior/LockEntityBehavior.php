<?php
/**
 * Created by PhpStorm.
 * User: пользователь
 * Date: 03.05.2018
 * Time: 15:40
 */

namespace common\models\behavior;

use common\models\Lock;
use yii\base\Behavior;

class LockEntityBehavior extends Behavior
{
    public $entity;

    public function lock(): bool
    {
        return Lock::create($this->entity);
    }

    public function unlock(): bool
    {
        Lock::free($this->entity);
        return true;
    }
}

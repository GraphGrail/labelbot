<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models\behavior;

use yii\base\Behavior;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

/**
 * Class DeletedAttributeBehavior
 * @package common\models\behavior
 * @property ActiveRecord $owner
 */
class DeletedAttributeBehavior extends Behavior
{

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    /**
     * @param ModelEvent $event
     * @return bool
     */
    public function beforeDelete($event)
    {
        $this->owner->setAttribute('deleted', true);
        $this->owner->save(false, ['deleted']);
        $event->isValid = false;
        return $event->isValid;
    }
}
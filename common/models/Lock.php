<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models;


class Lock extends ActiveRecord
{

    public static function tableName()
    {
        return 'lock_entity';
    }

    public static function create(object $object): bool
    {
        $result = false;
        try {
            $lock = new static();
            $lock->setAttributes($lock->createParams($object), false);
            $result = $lock->save();
        } catch (\Exception $e) {
            //Mysql duplicate key
        }
        return $result;
    }

    public static function free(object $object)
    {
        $lock = new static();
        if ($lock = $lock->findLock($object)) {
            $lock->delete();
        }
    }

    public function createParams(object $object): array
    {
        return [
            'entityName' => \get_class($object),
            'entityPk' => $object->id,
        ];
    }

    public function findLock(object $object): ?Lock {
        return self::find()->where($this->createParams($object))->one();
    }

}
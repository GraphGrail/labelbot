<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models;


class ActiveRecord extends \yii\db\ActiveRecord
{

    public function __toString()
    {
        if ($this->hasAttribute('name')) {
            return (string)$this->getAttribute('name');
        }
        return static::class;
    }
}
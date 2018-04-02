<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models\view;


use common\models\AssignedLabel;

class PreviewScoreWorkView
{
    protected $label;

    public function __construct(AssignedLabel $label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label->getLabel()->one();
    }

    public function getText()
    {
        return $this->label->getData()->one()->data;
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        return json_encode([
            'text' => $this->getText(),
            'label' => $this->getLabel()->text,
        ]);
    }

    public function toArray()
    {
        return [
            'text' => $this->getText(),
            'label' => $this->getLabel()->text,
        ];
    }
}
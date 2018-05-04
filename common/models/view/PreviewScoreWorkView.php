<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models\view;


use common\models\Data;
use common\models\DataLabel;
use common\models\Label;

class PreviewScoreWorkView
{
    protected $label;

    public function __construct(DataLabel $label)
    {
        $this->label = $label;
    }

    /**
     * @return Label
     */
    public function getLabel()
    {
        /** @var Label $label */
        $label = $this->label->getLabel()->one();
        return $label;
    }

    /**
     * @return string
     */
    public function getText() : string
    {
        /** @var Data $data */
        $data = $this->label->getData()->one();
        return $data->data;
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
            'label' => $this->getLabel()->buildPath(),
        ];
    }
}
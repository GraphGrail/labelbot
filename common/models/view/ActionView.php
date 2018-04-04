<?php
/**
 * @author Juriy Panasevich <u.panasevich@graphgrail.com>
 */

namespace common\models\view;


class ActionView
{
    protected $label;
    protected $url;
    protected $options = [];


    public function __construct($label = '', $url = '')
    {
        $this->label = $label;
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return ActionView
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->options)) {
            return null;
        }
        return $this->options[$name];
    }
}
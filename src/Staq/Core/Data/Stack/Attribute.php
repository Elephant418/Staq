<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack;

class Attribute implements \Stack\IAttribute
{


    /* ATTRIBUTES
     *************************************************************************/
    public $editable = TRUE;
    protected $seed;
    protected $defaultSeed;


    /* CONSTRUCTOR
     *************************************************************************/
    public function bySetting($model, $setting)
    {
        $class = 'Stack\\Attribute';
        if (is_string($setting)) {
            $class .= '\\' . ucfirst($setting);
        } else if (is_array($setting) && isset($setting['attribute'])) {
            $class .= '\\' . ucfirst($setting['attribute']);
        }
        if (strtolower($class) != strtolower(get_class($this))) {
            return (new $class)->bySetting($model, $setting);
        }
        $this->initBySetting($model, $setting);
        return $this;
    }

    public function initBySetting($model, $setting)
    {
        if (is_array($setting)) {
            if (isset($setting['default'])) {
                $this->defaultSeed = $setting['default'];
            }
        }
    }


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        if (is_null($this->seed)) {
            return $this->defaultSeed;
        }
        return $this->seed;
    }

    public function set($value)
    {
        if ( $this->editable ) {
            $this->seed = $value;
        }
    }


    /* PUBLIC DATABASE METHODS
     *************************************************************************/
    public function getSeed()
    {
        if (is_null($this->seed)) {
            return $this->defaultSeed;
        }
        return $this->seed;
    }

    public function setSeed($seed)
    {
        $this->seed = $seed;
    }


    /* HANDLER METHODS
     *************************************************************************/
    public function saveHandler()
    {
    }


    /* DEBUG METHODS
     *************************************************************************/
    public function __toString()
    {
        return '' . $this->get();
    }
}
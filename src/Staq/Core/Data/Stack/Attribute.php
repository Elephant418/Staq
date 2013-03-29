<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack;

class Attribute implements \Stack\IAttribute
{


    /*************************************************************************
    ATTRIBUTES
     *************************************************************************/
    protected $seed;


    /*************************************************************************
    CONSTRUCTOR
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
    }


    /*************************************************************************
    PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        return $this->seed;
    }

    public function set($value)
    {
        $this->seed = $value;
    }


    /*************************************************************************
    PUBLIC DATABASE METHODS
     *************************************************************************/
    public function getSeed()
    {
        return $this->seed;
    }

    public function setSeed($seed)
    {
        $this->seed = $seed;
    }


    /*************************************************************************
    DEBUG METHODS
     *************************************************************************/
    public function __toString()
    {
        return \Staq\Util::getStackQuery($this) . '( ' . $this->seed . ' )';
    }
}
<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class Selection extends Selection\__Parent
{


    /*************************************************************************
    ATTRIBUTES
     *************************************************************************/
    protected $options;
    protected $allowNull = TRUE;


    /*************************************************************************
    CONSTRUCTOR
     *************************************************************************/
    public function initBySetting($model, $setting)
    {
        if (is_array($setting)) {
            $setting = new \Stack\Util\ArrayObject($setting);
            $this->options = $setting->getAsArray('options');
            $this->allowNull = $setting->getAsBoolean('allowNull', TRUE);
        }
        if (!$this->allowNull && empty($this->options)) {
            throw new \Exception('No options defined for the selection attribute');
        }
    }


    /*************************************************************************
    PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        if (isset($this->options[$this->seed])) {
            return $this->options[$this->seed];
        }
        if (!$this->allowNull) {
            return reset($this->options);
        }
    }

    public function set($value)
    {
        $key = array_search($value, $this->options, TRUE);
        if ($key !== FALSE) {
            $this->seed = $key;
        }
    }


    /*************************************************************************
    PUBLIC DATABASE METHODS
     *************************************************************************/
    public function getSeed()
    {
        if ($this->allowNull || !is_null($this->seed)) {
            return $this->seed;
        }
        return reset(array_keys($this->options));
    }

    /*************************************************************************
    PUBLIC METHODS
     *************************************************************************/
    public function getOptions()
    {
        return $this->options;
    }

    public function allowNull()
    {
        return $this->allowNull;
    }
}
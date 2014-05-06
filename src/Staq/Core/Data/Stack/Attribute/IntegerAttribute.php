<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class IntegerAttribute extends IntegerAttribute\__Parent
{


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        return (int) parent::get();
    }

    public function set($value)
    {
        $this->seed = (int) $value;
    }


    /* PUBLIC DATABASE METHODS
     *************************************************************************/
    public function setSeed($seed)
    {
        $this->seed = (int) $seed;
    }
}
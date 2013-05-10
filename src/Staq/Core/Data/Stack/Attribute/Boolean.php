<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class Boolean extends Boolean\__Parent
{


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        $seed = (int) parent::get();
        return ($seed !== 0);
    }

    public function set($value)
    {
        $this->seed = $value ? 1 : 0;
    }


    /* PUBLIC DATABASE METHODS
     *************************************************************************/
    public function setSeed($seed)
    {
        $this->seed = (int)$seed;
    }
}
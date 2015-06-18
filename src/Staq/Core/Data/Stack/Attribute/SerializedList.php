<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class SerializedList extends SerializedList\__Parent
{


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        $unserialized = json_decode($this->seed, TRUE);
        \UArray::doConvertToArray($unserialized);
        return array_values($unserialized);
    }

    public function set($list)
    {
        \UArray::doConvertToArray($list);
        $list = array_values($list);
        $this->seed = json_encode($list);
    }

    public function add($item)
    {
        $list = $this->get();
        $list[] = $item;
        $this->set($list);
    }


    /* PUBLIC DATABASE METHODS
     *************************************************************************/
    public function getSeed()
    {
        return \UArray::convertToArray($this->seed);
    }
}
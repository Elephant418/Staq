<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class SerializedArray extends SerializedArray\__Parent
{


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        $list = json_decode($this->seed, TRUE);
        return \UArray::convertToArray($list);
    }

    public function set($list)
    {
        \UArray::convertToArray($list);
        $this->seed = json_encode($list);
        return $this;
    }
}
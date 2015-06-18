<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class SerializedList extends SerializedArray
{


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        $list = parent::get();
        return array_values($list);
    }

    public function set($list)
    {
        \UArray::doConvertToArray($list);
        $list = array_values($list);
        return parent::set($list);
    }

    public function add($add)
    {
        $list = $this->get();
        $list[] = $add;
        $this->set($list);
        return $this;
    }

    public function addList($addList)
    {
        $list = $this->get();
        $list = array_merge($list, $addList);
        $this->set($list);
        return $this;
    }
}
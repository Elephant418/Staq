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
        $list = array_values($list);
        $list = array_map($list, array($this, 'formatItem'));
        return $list;
    }

    public function set($list)
    {
        \UArray::doConvertToArray($list);
        $list = array_values($list);
        $list = array_filter($list, array($this, 'validateItem'));
        $list = array_map($list, array($this, 'formatItemSeed'));
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


    /* PRIVATE USER METHODS
     *************************************************************************/
    protected function validateItem($item) {
        return true;
    }
    
    protected function formatItemSeed($item) {
        return $item;
    }
    
    protected function formatItem($item) {
        return $item;
    }
}
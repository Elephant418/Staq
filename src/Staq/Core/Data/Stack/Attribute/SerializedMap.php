<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class SerializedMap extends SerializedMap\__Parent
{


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        $list = json_decode($this->seed, TRUE);
        \UArray::doConvertToArray($list);
        $result = [];
        foreach ($list as $key => $item) {
            $validation = $this->formatItem($item, $key);
            if ($validation) {
                $result[$key] = $item;
            }
        }
        return $result;
    }

    public function set($list)
    {
        \UArray::convertToArray($list);
        $result = [];
        foreach ($list as $key => $item) {
            $validation = $this->formatItemSeed($item, $key);
            if ($validation) {
                $result[$key] = $item;
            }
        }
        $this->seed = json_encode($result);
        return $this;
    }

    public function add($item, $key = null)
    {
        $list = $this->get();
        if (is_null($key)) {
            $list[] = $item;
        } else {
            $list[$key] = $item;
        }
        return $this->set($list);
    }


    /* PRIVATE USER METHODS
     *************************************************************************/
    protected function formatItem(&$item, &$key) {
        return true;
    }

    protected function formatItemSeed(&$item, &$key) {
        return true;
    }
}
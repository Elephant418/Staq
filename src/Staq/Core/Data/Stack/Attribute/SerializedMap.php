<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class SerializedMap extends SerializedMap\__Parent
{
    protected $map;


    /* PUBLIC USER GETTER METHODS
     *************************************************************************/
    public function reload()
    {
        $map = json_decode($this->seed, TRUE);
        \UArray::doConvertToArray($map);
        $result = [];
        foreach ($map as $key => $item) {
            $validation = $this->formatItem($item, $key);
            if ($validation) {
                $result[$key] = $item;
            }
        }
        $this->map = $result;
    }

    public function get()
    {
        if (is_null($this->map)) {
            $this->reload();
        }
        return $this->map;
    }


    /* PUBLIC USER SETTER METHODS
     *************************************************************************/
    public function set($map)
    {
        \UArray::convertToArray($map);
        $result = [];
        foreach ($map as $key => $item) {
            $validation = $this->formatItemSeed($item, $key);
            if ($validation) {
                $result[$key] = $item;
            }
        }
        $this->seed = json_encode($result);
        $this->map = $map;
        return $this;
    }

    public function add($item, $key = null)
    {
        $map = $this->get();
        if (is_null($key)) {
            $map[] = $item;
        } else {
            $map[$key] = $item;
        }
        $this->set($map);
        return $this;
    }

    public function remove($key)
    {
        $map = $this->get();
        if (isset($map[$key])) {
            unset($map[$key]);
            $this->set($map);
        }
        return $this;
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
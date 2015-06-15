<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack;

class Model extends \ArrayObject implements \Stack\IModel
{


    /* ATTRIBUTES
     *************************************************************************/
    public $id;
    protected $schemaAttributeNames = [];
    public $entity;


    /* GETTER
     *************************************************************************/
    public function exists()
    {
        return ($this->id !== NULL);
    }

    public function is($model)
    {
        return (\Staq\Util::getStackQuery($model) == \Staq\Util::getStackQuery($this));
    }

    public function name()
    {
        return $this->id;
    }


    /* CONSTRUCTOR
     *************************************************************************/
    public function __construct()
    {
        $this->setFlags(\ArrayObject::ARRAY_AS_PROPS);
        $this->entity = $this->newEntity();
        $this->importSchema();
    }

    protected function newEntity()
    {
        $class = 'Stack\\Entity';
        $subQuery = \Staq\Util::getStackSubQuery($this);
        if ($subQuery) {
            $class .= '\\' . $subQuery;
        }
        return new $class;
    }

    protected function importSchema()
    {
        $settings = (new \Stack\Setting)->parse($this);
        foreach ($settings->getAsArray('schema') as $name => $setting) {
            $this->addAttribute($name, $setting);
        }
    }

    protected function addAttribute($name, $setting)
    {
        $attribute = (new \Stack\Attribute)->bySetting($this, $setting);
        $this->schemaAttributeNames[] = $name;
        parent::offsetSet($name, $attribute);
    }

    public function keys()
    {
        return array_keys($this->getArrayCopy());
    }

    protected function initialize()
    {

    }


    /* INITIALIZATION
     *************************************************************************/
    public function byData($data)
    {
        \UArray::doConvertToArray($data);
        $model = new $this;
        $model->id = $this->entity->extractId($data);
        foreach ($data as $name => $seed) {
            $attribute = $model->getAttribute($name);
            if (is_object($attribute)) {
                $attribute->setSeed($seed);
            } else {
                $model->set($name, $seed);
            }
        }
        $model->initialize();
        return $model;
    }


    /* PUBLIC DATABASE REQUEST
     *************************************************************************/
    public function delete()
    {
        if ($this->entity->delete($this)) {
            $this->id = NULL;
        }
        return $this;
    }

    public function save()
    {
        $this->id = $this->entity->save($this);
        foreach($this->keys() as $name) {
            $attribute = $this->getAttribute($name);
            if (\Staq\Util::isStack($attribute, 'Stack\\Attribute')) {
                $attribute->saveHandler();
            }
        }
        return $this;
    }

    public function extractSeeds()
    {
        $data = [];
        foreach ($this->keys() as $name) {
            $attribute = $this->getAttribute($name);
            if (\Staq\Util::isStack($attribute, 'Stack\\Attribute')) {
                $attribute = $attribute->getSeed();
            }
            $data[$name] = $attribute;
        }
        return $data;
    }


    /* SPECIFIC MODEL ACCESSOR METHODS
     *************************************************************************/
    public function hasAttribute($index)
    {
        return $this->offsetExists($index);
    }
    
    public function getAttribute($index)
    {
        if ($this->hasAttribute($index)) {
            return parent::offsetGet($index);
        }
    }


    /* HERITED ACCESSOR METHODS
     *************************************************************************/
    public function get($index)
    {
        return $this->offsetGet($index);
    }

    public function offsetGet($index)
    {
        $attribute = $this->getAttribute($index);
        if (\Staq\Util::isStack($attribute, 'Stack\\Attribute')) {
            return $attribute->get();
        } else {
            return $attribute;
        }
    }

    public function set($index, $newVal)
    {
        $this->offsetSet($index, $newVal);
        return $this;
    }

    public function offsetSet($index, $newVal)
    {
        $attribute = $this->getAttribute($index);
        if (\Staq\Util::isStack($attribute, 'Stack\\Attribute')) {
            $attribute->set($newVal);
        } else {
            parent::offsetSet($index, $newVal);
        }
    }

    public function offsetUnset($index)
    {
        $this->offsetSet($index, NULL);
    }


    /* PHP MEHODS
     *************************************************************************/
    public function __toString()
    {
        return get_class($this) . '(' . $this->id . ')';
    }
}

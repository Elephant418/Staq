<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute\Relation;

class OneToMany extends OneToMany\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $changed = FALSE;
    protected $model;
    protected $remoteModels = NULL;
    protected $remoteModelType;
    protected $relatedAttributeName;
    protected $filterList = [];


    /* CONSTRUCTOR
     *************************************************************************/
    public function initBySetting($model, $setting)
    {
        parent::initBySetting($model, $setting);
        $this->model = $model;
        if (is_array($setting)) {
            if (!isset($setting['remote_class_type'])) {
                throw new \Stack\Exception\MissingSetting('"remote_class_type" missing for the OneToMany relation.');
            }
            if (!isset($setting['related_attribute_name'])) {
                throw new \Stack\Exception\MissingSetting('"related_attribute_name" missing for the OneToMany relation.');
            }
            if (isset($setting['filter_list'])) {
                $this->filterList = $setting['filter_list'];
            }
            $this->remoteModelType = $setting['remote_class_type'];
            $this->relatedAttributeName = $setting['related_attribute_name'];
        }
    }


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function reload()
    {
        $class = $this->getRemoteClass();
        $this->remoteModels = (new $class)->entity->fetchByRelated($this->relatedAttributeName, $this->model);
    }

    public function get()
    {
        if (is_null($this->remoteModels)) {
            $this->reload();
        }
        return $this->remoteModels;
    }

    public function getIds()
    {
        $ids = [];
        foreach ($this->get() as $model) {
            $ids[] = $model->id;
        }
        return $ids;
    }

    public function set($remoteModels)
    {
        $this->remoteModels = [];
        $this->changed = TRUE;
        \UArray::doConvertToArray($remoteModels);
        foreach( $remoteModels as $model ) {
            if (empty($model)) {
                $remoteModel = $this->getRemoteModel();
            } else if (is_numeric($model)) {
                $model = $this->getRemoteModel()->entity->fetchById($model);
            } else if (!\Staq\Util::isStack($model, $this->getRemoteClass())) {
                $message = 'Input of type "' . $this->getRemoteClass() . '", but "' . gettype($model) . '" given.';
                throw new \Stack\Exception\NotRightInput($message);
            }
            if ($model && $model->exists()) {
                $this->remoteModels[] = $model;
            }
        }
        return $this;
    }


    /* PUBLIC DATABASE METHODS
     *************************************************************************/
    public function getSeed()
    {
        return NULL;
    }

    public function setSeed($seed)
    {
    }


    /* HANDLER METHODS
     *************************************************************************/
    public function saveHandler()
    {
        if ($this->changed) {
            $class = $this->getRemoteClass();
            (new $class)->entity->updateRelated($this->relatedAttributeName, $this->getIds(), $this->model);
        }
    }


    /* PUBLIC METHODS
     *************************************************************************/
    public function getRelatedModels()
    {
        $class = $this->getRemoteClass();
        return (new $class)->entity->fetchAll();
    }

    public function getRemoteModel()
    {
        $class = $this->getRemoteClass();
        return new $class;
    }

    public function getRemoteModelType()
    {
        return $this->remoteModelType;
    }

    public function getRemoteClass()
    {
        return $class = 'Stack\\Model\\' . $this->remoteModelType;
    }


    /* DEBUG METHODS
     *************************************************************************/
    public function __toString()
    {
        $relateds = $this->get();
        array_walk( $relateds, function(&$a) {
            $a = $a->name();
        });
        return implode( ', ', $relateds );
    }
}
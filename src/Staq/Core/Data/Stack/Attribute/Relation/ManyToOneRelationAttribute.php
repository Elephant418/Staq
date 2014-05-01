<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute\Relation;

class ManyToOneRelationAttribute extends ManyToOne\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $remoteModel;
    protected $remoteModelType;


    /* CONSTRUCTOR
     *************************************************************************/
    public function initBySetting($model, $setting)
    {
        parent::initBySetting($model, $setting);
        if (is_array($setting)) {
            if (isset($setting['remote_class_type'])) {
                $this->remoteModelType = $setting['remote_class_type'];
            }
        }
    }


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function reload()
    {
        $class = $this->getRemoteClass();
        $this->remoteModel = (new $class)->entity->fetchById($this->seed);
    }

    public function get()
    {
        if (is_null($this->remoteModel) && isset($this->seed)) {
            $this->reload();
        }
        return $this->remoteModel;
    }

    public function set($model)
    {
        if (empty($model)) {
            $model = $this->getRemoteModel();
        } else if (is_numeric($model)) {
            $model = $this->getRemoteModel()->entity->fetchById($model);
        } else if (!\Staq\Util::isStack($model, $this->getRemoteClass())) {
            $message = 'Input of type "' . $this->getRemoteClass() . '", but "' . gettype($model) . '" given.';
            throw new \Stack\Exception\NotRightInput($message);
        }
        if ($model && $model->exists()) {
            $this->remoteModel = $model;
            $this->seed = $model->id;
        } else {
            $this->seed = NULL;
            $this->remoteModel = NULL;
        }
        return $this;
    }


    /* PUBLIC DATABASE METHODS
     *************************************************************************/
    public function getSeed()
    {
        return $this->seed;
    }

    public function setSeed($seed)
    {
        $this->seed = $seed;
        $this->remoteModel = NULL;
    }


    /* PUBLIC METHODS
     *************************************************************************/
    public function getRelatedModels()
    {
        return $this->getRemoteModel()->entity->fetchAll();
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
        $related = $this->get();
        if ( $related ) {
            return '' . $related->name();
        }
        return '';
    }
}
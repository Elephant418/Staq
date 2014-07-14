<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute\Relation;

class FetchAlias extends FetchAlias\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    public $editable = false;
    protected $remoteModels = null;
    protected $fetchMethod;
    protected $model;
    protected $initialized = false;


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
            if (!isset($setting['fetch_method'])) {
                throw new \Stack\Exception\MissingSetting('"fetch_method" missing for the FetchAlias relation.');
            }
            $this->remoteModelType = $setting['remote_class_type'];
            $this->fetchMethod = $setting['fetch_method'];
        }
    }


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function reload()
    {
        $entity = $this->getRemoteEntity();
        $fetchMethod = $this->fetchMethod;
        $this->remoteModels = $entity->$fetchMethod($this->model);
        $this->initialized = true;
    }

    public function get()
    {
        if (! $this->initialized) {
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


    /* PUBLIC METHODS
     *************************************************************************/
    public function getRemoteEntity()
    {
        $entityClass = $this->getRemoteEntityClass();
        return new $entityClass;
    }

    public function getRemoteModelType() {
        return $this->remoteModelType;
    }

    public function getRemoteEntityClass()
    {
        return $class = 'Stack\\Entity\\' . $this->remoteModelType;
    }


    /* DEBUG METHODS
     *************************************************************************/
    public function __toString()
    {
        return implode( ' - ', $this->get() );
    }
}
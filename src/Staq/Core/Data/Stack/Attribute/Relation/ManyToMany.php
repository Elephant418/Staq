<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute\Relation;

class ManyToMany extends OneToMany
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $table;
    protected $remoteAttributeName;


    /* CONSTRUCTOR
     *************************************************************************/
    public function initBySetting($model, $setting)
    {
        parent::initBySetting($model, $setting);
        if (is_array($setting)) {
            if (!isset($setting['table'])) {
                throw new \Stack\Exception\MissingSetting('"table" missing for the ManyToMany relation.');
            }
            if (!isset($setting['remote_attribute_name'])) {
                throw new \Stack\Exception\MissingSetting('"remote_attribute_name" missing for the ManyToMany relation.');
            }
            $this->table = $setting['table'];
            $this->remoteAttributeName = $setting['remote_attribute_name'];
        }
    }


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function reload()
    {
        $class = $this->getRemoteClass();
        $this->remoteModels = (new $class)->entity->fetchByRelatedThroughTable($this->table, $this->remoteAttributeName, $this->relatedAttributeName, $this->model, $this->filterList);
    }

    public function get()
    {
        if (is_null($this->remoteModels)) {
            $this->reload();
        }
        return $this->remoteModels;
    }


    /* HANDLER METHODS
     *************************************************************************/
    public function saveHandler()
    {
        if ($this->changed) {
            $class = $this->getRemoteClass();
            (new $class)->entity->updateRelatedThroughTable($this->table, $this->remoteAttributeName, $this->relatedAttributeName, $this->getIds(), $this->model, $this->filterList);
        }
    }
}
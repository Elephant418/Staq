<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\File;

use \Michelf\MarkdownExtra;

class Entity
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $name;
    protected $idField = "name";


    /* CONSTRUCTOR
     *************************************************************************/
    public function __construct()
    {
        $settings = (new \Stack\Setting)->parse($this);
        $this->name = \Staq\Util::getStackSubQuery($this, '_');
        if (isset($settings['idField'])) {
            $this->idField = $settings['idField'];
        }
    }


    /* FETCHING METHODS
     *************************************************************************/
    public function fetchById($id)
    {
        $data = [];
        $folder = 'data/'.$this->name.'/'.$id;
        $metaPath = \Staq::App()->getFilePath($folder.'.json');
        if ($metaPath) {
            $data = json_decode(file_get_contents($metaPath), TRUE);
        }
        $contentPath = \Staq::App()->getFilePath($folder.'.md');
        if ($contentPath) {
            $data[$this->idField] = $id;
            $data['content'] = MarkdownExtra::defaultTransform(file_get_contents($contentPath));
        }
        return $this->resultAsModel($data);
    }

    public function fetchByIds($ids)
    {
        $models = [];
        foreach ($ids as $id) {
            $model = $this->fetchById($id);
            if ($model->exists()) {
                $models[$id] = $model;
            }
        }
        return $models;
    }

    public function fetchAll($limit = NULL, &$rows = FALSE)
    {
        $path = '/data/'.$this->name;
        $folders = \Staq::App()->getExtensions();
        array_walk($folders, function (&$a) use ($path) {
            $a = realpath($a . $path);
        });
        $folders = array_filter($folders, function ($a) {
            return (!empty($a));
        });
        $ids = [];
        foreach ($folders as $folder) {
            foreach (glob($folder.'/*\.json') as $filename) {
                $ids[] = \UString::substrBeforeLast(basename($filename), '.');
            }
            foreach (glob($folder.'/*\.md') as $filename) {
                $ids[] = \UString::substrBeforeLast(basename($filename), '.');
            }
        }
        return $this->fetchByIds(array_unique($ids));
    }

    public function extractId(&$data)
    {
        $id = NULL;
        if (isset($data[$this->idField])) {
            $id = $data[$this->idField];
            unset($data[$this->idField]);
        }
        return $id;
    }


    /* PRIVATE CORE METHODS
     *************************************************************************/
    protected function getModel()
    {
        $modelClass = 'Stack\\Model\\' . \Staq\Util::getStackSubQuery($this);
        return new $modelClass;
    }

    protected function resultAsModelList($data)
    {
        $list = [];
        $model = $this->getModel();
        foreach ($data as $item) {
            $list[] = $model->byData($item);
        }
        return $list;
    }

    protected function resultAsModel($data)
    {
        return $this->getModel()->byData($data);
    }
}

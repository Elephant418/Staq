<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Storage\File;

use \Michelf\MarkdownExtra;

class Entity extends \Staq\Core\Data\Stack\Storage\Entity
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $name;
    protected $idField = "name";
    protected $extensions = ['json', 'md', 'html', 'php', 'txt'];


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
        foreach ($this->globDataFile($id) as $filename) {
            // TODO merge it
            $data = array_merge($data, $this->fetchFileData($filename));
        }
        return $this->resultAsModel($data);
    }

    public function fetchByField($field, $value, $limit = NULL)
    {
        $models = $this->fetchAll();
        \UArray::doFilterBy($models, $field, $value);
        if ($limit) {
            $models = array_slice($models, 0, $limit);
        }
        return $models;
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
        $ids = [];
        foreach ($this->globDataFile('*') as $filename) {
            $ids[] = \UString::substrBeforeLast(basename($filename), '.');
        }
        return $this->fetchByIds(array_unique($ids));
    }


    /* MODEL METHODS
     *************************************************************************/
    public function extractId(&$data)
    {
        if (isset($data[$this->idField])) {
            return $data[$this->idField];
        }
    }


    /* PROTECTED METHODS
     *************************************************************************/
    public function fetchFileData($filePath)
    {
        $data = [];
        $data[$this->idField] = \UString::substrBeforeLast(basename($filePath), '.');
        $extension = \UString::substrAfterLast(basename($filePath), '.');
        if ($extension == 'json') {
            $data = array_merge($data, json_decode(file_get_contents($filePath), TRUE));
        } else if ($extension == 'md'){
            $data['content'] = MarkdownExtra::defaultTransform(file_get_contents($filePath));
        } else if ($extension == 'html'){
            $data['content'] = file_get_contents($filePath);
        } else if ($extension == 'php'){
            ob_start();
            include($filePath);
            $content = ob_get_contents();
            ob_end_clean();
            $data['content'] = $content;
        } else if ($extension == 'txt'){
            $data['content'] = str_replace(PHP_EOL, HTML_EOL, htmlentities(file_get_contents($filePath)));
        }
        return $data;
    }

    public function globDataFile($pattern)
    {
        $path = '/data/'.$this->name;
        $folders = \Staq::App()->getExtensions();
        array_walk($folders, function (&$a) use ($path) {
            $a = realpath($a . $path);
        });
        $folders = array_filter($folders, function ($a) {
            return (!empty($a));
        });
        $files = [];
        foreach ($folders as $folder) {
            $extensions = implode(',', $this->extensions);
            foreach (glob($folder.'/'.$pattern.'\.{'.$extensions.'}', GLOB_BRACE) as $filename) {
                $files[] = $filename;
            }
        }
        return $files;
    }
}

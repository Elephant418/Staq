<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Storage\File;

use \Michelf\MarkdownExtra;

class Entity extends \Staq\Core\Data\Stack\Storage\Entity implements \Stack\IEntity
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $name;
    protected $folder;
    protected $idField = "name";
    protected $extensions = ['json', 'md', 'html', 'php', 'txt'];


    /* CONSTRUCTOR
     *************************************************************************/
    public function __construct()
    {
        $settings = (new \Stack\Setting)->parse($this);
        $this->name = \Staq\Util::getStackSubQuery($this, '_');
        $this->folder = '/data/'.$this->name;
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
            $data = array_merge($data, $this->fetchFileData($id, $filename));
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

    public function fetchAll($limit=NULL, $offset=NULL, &$count=FALSE)
    {
        $ids = [];
        foreach ($this->globDataFile('*') as $filename) {
            $ids[] = \UString::substrBeforeLast(basename($filename), '.');
        }
        $ids = array_unique($ids);
        if ($count !== FALSE) {
            $count = count($ids);
        }
        if (is_null($offset)) {
            $offset = 0;
        }
        if (!is_null($limit)) {
            $ids = array_slice($ids, $offset, $offset+$limit);
        }
        return $this->fetchByIds($ids);
    }


    /* MODEL METHODS
     *************************************************************************/
    public function extractId(&$data)
    {
        if (isset($data[$this->idField])) {
            return $data[$this->idField];
        }
    }

    public function delete($model = NULL)
    {
        $deleted = TRUE;
        foreach ($this->globDataFile($model->id) as $filename) {
            $deleted = $deleted && unlink($filename);
        }
        return $deleted;
    }

    public function save($model)
    {
        $id = $model[$this->idField];
        $filename = \Staq::App()->getFilePath().'/'.$this->folder.'/'.$id;
        $data = $model->extractSeeds();
        if (isset($data['content'])) {
            // TODO: if the model exists verify that it is a markdown file
            $converter = new \Markdownify\ConverterExtra;
            file_put_contents($filename.'.md', $converter->parseString($model->content));
            unset($data['content']);
        }
        unset($data[$this->idField]);
        file_put_contents($filename.'.json', json_encode($data));
        return $id;
    }


    /* PROTECTED METHODS
     *************************************************************************/
    public function fetchFileData($id, $filePath)
    {
        $data = [];
        $data[$this->idField] = $id;
        $extension = \UString::substrAfterLast(basename($filePath), '.');
        if ($extension == 'json') {
            $json = json_decode(file_get_contents($filePath), TRUE);
            if (is_array($json)) {
                $data = array_merge($data, $json);
            }
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
        $path = $this->folder;
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
        return array_reverse($files);
    }
}

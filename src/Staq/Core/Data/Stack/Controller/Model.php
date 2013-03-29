<?php

namespace Staq\Core\Data\Stack\Controller;

class Model extends Model\__Parent
{


    /*************************************************************************
    ACTION METHODS
     *************************************************************************/
    public function actionList()
    {
        $models = $this->getNewEntity()->fetchAll();
        $page = (new \Stack\View)->byName($this->getModelName(), 'Model_List');
        $page['content'] = $models;
        return $page;
    }

    public function actionView($id)
    {
        $model = $this->getNewEntity()->fetchById($id);
        if ($model->exists()) {
            $page = (new \Stack\View)->byName($this->getModelName(), 'Model_View');
            $page['content'] = $model;
            return $page;
        }
    }


    /*************************************************************************
    PUBLIC METHODS
     *************************************************************************/
    public function getRouteAttributes($model)
    {
        $attributes = [];
        $attributes['id'] = $model->id;
        $attributes['name'] = \Staq\Util::smartUrlEncode($model->name());
        return $attributes;
    }


    /*************************************************************************
    PRIVATE METHODS
     *************************************************************************/
    protected function getModelName()
    {
        return \Staq\Util::getStackSubSubQuery($this->modelClass());
    }

    protected function getModelClass()
    {
        return 'Stack\\Model\\' . \Staq\Util::getStackSubQuery($this);
    }

    protected function getEntityClass()
    {
        return 'Stack\\Entity\\' . \Staq\Util::getStackSubQuery($this);
    }

    protected function getNewModel()
    {
        $class = $this->getModelClass();
        return new $class;
    }

    protected function getNewEntity()
    {
        $class = $this->getEntityClass();
        return new $class;
    }
}
<?php

namespace Staq\Core\Data\Stack\Controller;

class ModelController extends Model\__Parent
{


    /* ACTION METHODS
     *************************************************************************/
    public function actionList()
    {
        $models = $this->getNewEntity()->fetchAll();
        $view = $this->createView('list');
        $view['content'] = $models;
        return $view;
    }

    public function actionView($id)
    {
        $model = $this->getNewEntity()->fetchById($id);
        if ($model->exists()) {
            $view = $this->createView();
            $view['content'] = $model;
            return $view;
        }
    }


    /* PUBLIC METHODS
     *************************************************************************/
    public function getRouteAttributes($model)
    {
        $attributes = [];
        $attributes['id'] = $model->id;
        $attributes['name'] = \Staq\Util::smartUrlEncode($model->name());
        return $attributes;
    }


    /* PRIVATE METHODS
     *************************************************************************/
    protected function createView($action='view')
    {
        $view = (new \Stack\View)->byName($this->getModelName(), 'Model_' . ucfirst($action));
        $view['controller'] = $this->getModelName();
        $view['controllerAction'] = $action;
        return $view;
    }

    protected function getModelName()
    {
        return \Staq\Util::getStackSubSubQuery($this);
    }

    protected function getModelClass()
    {
        return 'Stack\\Model\\' . $this->getModelName();
    }

    protected function getEntityClass()
    {
        return 'Stack\\Entity\\' . $this->getModelName();
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
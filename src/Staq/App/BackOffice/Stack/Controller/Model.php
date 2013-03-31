<?php

namespace Staq\App\BackOffice\Stack\Controller;

use \Stack\Util\UINotification as Notif;

class Model
{


    /*************************************************************************
    ACTION METHODS
     *************************************************************************/
    public function actionList($type)
    {
        $view = $this->createView('list', $type);
        $fields = (new \Stack\Setting)
            ->parse('BackOffice')
            ->get('list.' . $type);
        if (empty($fields)) {
            $fields = ['id'];
        }
        $view['fields'] = $fields;
        $view['models'] = $this->getNewEntity($type)->fetchAll();
        return $view;
    }

    public function actionView($type, $id)
    {
        $model = $this->getNewEntity($type)->fetchById($id);
        if ($model->exists()) {
            $view = $this->createView('view', $type);
            $view['model'] = $model;
            return $view;
        }
    }

    public function actionCreate($type)
    {
        $model = $this->getNewModel($type);
        return $this->genericActionEdit($type, $model);
    }

    public function actionEdit($type, $id)
    {
        $model = $this->getNewEntity($type)->fetchById($id);
        if ($model->exists()) {
            return $this->genericActionEdit($type, $model);
        }
    }

    public function actionDelete($type, $id)
    {
        $model = $this->getNewEntity($type)->fetchById($id);
        if ($model->exists()) {
            $model->delete();
            if ($model->exists()) {
                Notif::error('Model not deleted.');
                $this->redirectView($type, $model);
            } else {
                Notif::success('Model deleted.');
                $this->redirectList($type);
            }
        }
    }


    /*************************************************************************
    PRIVATE METHODS
     *************************************************************************/
    protected function createView($action, $type)
    {
        $view = (new \Stack\View)->byName($type, 'Model_' . ucfirst($action));
        $view['controller'] = $type;
        $view['controllerAction'] = $action;
        return $view;
    }

    protected function genericActionEdit($type, $model)
    {
        if (isset($_POST['model'])) {
            foreach ($_POST['model'] as $name => $value) {
                $model->set($name, $value);
            }
            if ($model->save()) {
                Notif::success('Model saved.');
            } else {
                Notif::error('Model not saved.');
            }
            $this->redirectView($type, $model);
        }
        $view = $this->createView('edit', $type);
        $view['model'] = $model;
        return $view;
    }


    /*************************************************************************
    REDIRECT METHODS
     *************************************************************************/
    protected function redirectView($type, $model)
    {
        $params = [];
        $params['type'] = $type;
        $params['id'] = $model->id;
        \Staq\Util::httpRedirectUri(\Staq::App()->getUri($this, 'view', $params));
    }

    protected function redirectList($type)
    {
        $params = [];
        $params['type'] = $type;
        \Staq\Util::httpRedirectUri(\Staq::App()->getUri($this, 'list', $params));
    }


    /*************************************************************************
    PRIVATE METHODS
     *************************************************************************/
    protected function getModelClass($type)
    {
        return 'Stack\\Model\\' . $type;
    }

    protected function getEntityClass($type)
    {
        return 'Stack\\Entity\\' . $type;
    }

    protected function getNewModel($type)
    {
        $class = $this->getModelClass($type);
        return new $class;
    }

    protected function getNewEntity($type)
    {
        $class = $this->getEntityClass($type);
        return new $class;
    }
}
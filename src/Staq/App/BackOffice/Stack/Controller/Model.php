<?php

namespace Staq\App\BackOffice\Stack\Controller;

use \Stack\Util\UINotification as Notif;

class Model
{


    /* ACTION METHODS
     *************************************************************************/
    public function actionList($type)
    {
        $view = $this->createView('list', $type);
        $fields = (new \Stack\Setting)
            ->parse('BackOffice')
            ->get('list.' . $type);
        if (empty($fields)) {
            $fields = ['name()'];
        }
        $view['fields'] = $fields;
        $view['models'] = $this->getNewEntity($type)->fetchAll();
        return $view;
    }

    public function actionView($id, $type=NULL, $action='view')
    {
        if (is_null($type)) {
            $type = $this->getModelName();
        }
        $model = $this->getNewEntity($type)->fetchById($id);
        if ($model->exists()) {
            $view = $this->createView($action, $type);
            $view['model'] = $model;
            return $view;
        }
    }

    public function actionPreview($id, $type=NULL)
    {
        return $this->actionView($id, $type, 'preview');
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
                $this->redirectPreview($type, $model);
            } else {
                Notif::success('Model deleted.');
                $this->redirectList($type);
            }
        }
    }


    /* PRIVATE METHODS
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
            $this->saveHandler($model);
            if ($model->save()) {
                Notif::success('Model saved.');
            } else {
                Notif::error('Model not saved.');
            }
            $this->redirectPreview($type, $model);
        }
        $view = $this->createView('edit', $type);
        $view['model'] = $model;
        return $view;
    }


    /* HANDLER METHODS
     *************************************************************************/
    protected function saveHandler($model)
    {
    }


    /* REDIRECT METHODS
     *************************************************************************/
    protected function redirectPreview($type, $model)
    {
        $params = [];
        $params['type'] = $type;
        $params['id'] = $model->id;
        \Staq\Util::httpRedirectUri(\Staq::App()->getUri($this, 'preview', $params));
    }

    protected function redirectList($type)
    {
        $params = [];
        $params['type'] = $type;
        \Staq\Util::httpRedirectUri(\Staq::App()->getUri($this, 'list', $params));
    }


    /* PRIVATE METHODS
     *************************************************************************/
    protected function getModelName()
    {
        return \Staq\Util::getStackSubSubQuery($this);
    }

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
<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack;

class Controller implements \Stack\IController
{


    /*************************************************************************
    ATTRIBUTES
     *************************************************************************/
    protected $routes = [];
    public static $setting = [];


    /*************************************************************************
    GETTER
     *************************************************************************/
    public function getRoutes()
    {
        return $this->routes;
    }


    /*************************************************************************
    CONSTRUCTOR
     *************************************************************************/
    public function __construct()
    {
        $this->initializeRoutes();
    }


    /*************************************************************************
    ACTION METHODS
     *************************************************************************/
    public function actionView()
    {
        return (new \Stack\View)->byName(\Staq\Util::getStackSubQuery($this));
    }


    /*************************************************************************
    PRIVATE METHODS
     *************************************************************************/
    protected function createView($action='view')
    {
        $controller = \Staq\Util::getStackSubQuery($this);
        $view = (new \Stack\View)->byName($action, $controller);
        $view['controller'] = $controller;
        $view['controllerAction'] = $action;
        return $view;
    }
    protected function initializeRoutes()
    {
        $setting = (new \Stack\Setting)->parse($this);
        foreach ($setting->getAsArray('route') as $action => $setting) {
            $this->routes[$action] = (new \Stack\Route)->bySetting($this, $action, $setting);
        }
    }
}

?>
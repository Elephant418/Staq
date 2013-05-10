<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack;

class Application extends Application\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $router;
    protected $controllers = [];


    /* GETTER
     *************************************************************************/
    public function getController($name)
    {
        return $this->router->getController($name);
    }

    public function getUri($controller, $action, $parameters)
    {
        return $this->router->getUri($controller, $action, $parameters);
    }

    public function getCurrentUri()
    {
        return $this->router->getCurrentUri();
    }

    public function getLastException()
    {
        return $this->router->getLastException();
    }


    /* SETTER
     *************************************************************************/
    public function addController($uri, $controller)
    {
        $this->controllers[] = func_get_args();
        return $this;
    }


    /* INITIALIZATION
     *************************************************************************/
    public function initialize()
    {
        parent::initialize();
        $this->router = new \Stack\Router();
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = \UString::substrBefore($_SERVER['REQUEST_URI'], '?');
            $uri = rawurldecode($uri);
            \UString::doNotStartWith($uri, $this->baseUri);
            \UString::doStartWith($uri, '/');
            $this->router->setUri($uri);
        }
    }


    /* PUBLIC METHODS
     *************************************************************************/
    public function run()
    {
        $this->router->initialize($this->controllers);
        $this->controllers = [];
        echo $this->router->resolve();
    }
}

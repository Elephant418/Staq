<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack;

class Route
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $callable;
    protected $uri;
    protected $exceptions = [];
    protected $aliases = [];
    protected $parameters = [];


    /* CONSTRUCTOR
     *************************************************************************/
    public function __construct($callable = NULL, $uri = NULL, $exceptions = [], $aliases = [])
    {
        \UArray::doConvertToArray($exceptions);
        \UArray::doConvertToArray($aliases);
        $this->callable = $callable;
        $this->uri = $uri;
        $this->exceptions = $exceptions;
        $this->aliases = $aliases;
    }

    public function bySetting($controller, $action, $setting)
    {
        if (!\UString::isStartWith($action, 'action')) {
            $action = 'action' . ucfirst($action);
        }
        $callable = [$controller, $action];
        if (!is_callable($callable)) {
            $message = get_class($controller) . '::' . $action . ' is not callable';
            throw new \Stack\Exception\NoCallable($message);
        }
        $uri = NULL;
        $exceptions = [];
        $aliases = [];
        if (is_array($setting)) {
            $uri = isset($setting['uri']) ? $setting['uri'] : $uri;
            $exceptions = isset($setting['exceptions']) ? $setting['exceptions'] : $exceptions;
            $aliases = isset($setting['aliases']) ? $setting['aliases'] : $aliases;
        } else if (is_string($setting)) {
            $uri = $setting;
        }
        return new $this($callable, $uri, $exceptions, $aliases);
    }


    /* PUBLIC METHODS
     *************************************************************************/
    public function getUri($parameters = [])
    {
        $uri = $this->uri;
        foreach ($parameters as $name => $value) {
            if (!is_numeric($name)) {
                $uri = str_replace(':' . $name, $value, $uri);
                unset($parameters[$name]);
            }
        }
        ksort($parameters);
        foreach ($parameters as $value) {
            $uri = preg_replace('#^([^:]*):\w+#', '${1}' . $value, $uri);
        }
        $uri = preg_replace('#\(([^):]*)\)#', '${1}', $uri);
        $uri = preg_replace('#\([^)]*\)#', '', $uri);
        $uri = preg_replace('#:(\w+)#', '', $uri);
        return $uri;
    }

    public function callAction()
    {
        if (is_array($this->callable)) {
            $reflection = new \ReflectionMethod($this->callable[0], $this->callable[1]);
        } else {
            $reflection = new \ReflectionFunction($this->callable);
        }
        $parameters = [];
        foreach ($reflection->getParameters() as $parameter) {
            if (!$parameter->canBePassedByValue()) {
                throw new \Stack\Exception\ControllerActionDefinition('A controller could not have parameter passed by reference');
            }
            if (isset($this->parameters[$parameter->name])) {
                $parameters[] = $this->parameters[$parameter->name];
            } else if ($parameter->isDefaultValueAvailable()) {
                $parameters[] = $parameter->getDefaultValue();
            } else {
                throw new \Stack\Exception\ControllerActionDefinition('The current uri does not provide a value for the parameter "' . $parameter->name . '"');
            }
        }
        return call_user_func_array($this->callable, $parameters);
    }

    public function isRouteCatchUri($uri)
    {
        if ($this->isUriMatch($uri, $this->uri)) {
            return TRUE;
        }
        foreach ($this->aliases as $alias) {
            if ($this->isUriMatch($uri, $alias)) {
                return $this->getUri($this->parameters);
            }
        }
        return FALSE;
    }

    public function isRouteCatchException($exception)
    {
        $parameters = [];
        $result = FALSE;
        foreach ($this->exceptions as $matchException) {
            if (is_numeric($matchException)) {
                if ($exception->getCode() == $matchException) {
                    $result = TRUE;
                }
            } else if (\Staq\Util::isStack($exception)) {
                if (\Staq\Util::getStackSubQuery($exception) === $matchException) {
                    $result = TRUE;
                }
            } else if (get_class($exception) === $matchException) {
                $result = TRUE;
            }
        }
        if ($result) {
            $parameters = $this->getParametersFromException($exception);
        }
        $this->parameters = $parameters;
        return $result;
    }


    /* PROTECTED METHODS
     *************************************************************************/
    protected function isUriMatch($uri, $refer)
    {
        $pattern = str_replace(['.', '+', '?'], ['\.', '\+', '\?'], $refer);
        $pattern = preg_replace('#\*#', '.*', $pattern);
        $pattern = preg_replace('#\(([^)]*)\)#', '(?:\1)?', $pattern);
        $pattern = preg_replace('#\:(\w+)#', '(?<\1>[a-zA-Z0-9_+ -]+)', $pattern);
        $pattern = '#^' . $pattern . '/?$#';
        $parameters = [];
        $result = preg_match($pattern, $uri, $parameters);
        if ($result) {
            foreach (array_keys($parameters) as $key) {
                if (is_numeric($key)) {
                    unset($parameters[$key]);
                }
            }
        } else {
            $parameters = [];
        }
        $this->parameters = $parameters;
        return $result;
    }

    protected function getParametersFromException($exception)
    {
        $parameters = [];
        $parameters['code'] = $exception->getCode();
        $parameters['exception'] = $exception;
        if (\Staq\Util::isStack($exception)) {
            $parameters['query'] = \Staq\Util::getStackQuery($exception);
            $parameters['name'] = \Staq\Util::getStackSubQuery($exception);
        } else {
            $parameters['query'] = get_class($exception);
            $parameters['name'] = get_class($exception);
        }
        return $parameters;
    }


    /* DEBUG METHODS
     *************************************************************************/
    public function __toString()
    {
        $str = 'Route( ' . $this->uri . ' => ';
        if (is_array($this->callable)) {
            $controller = $this->callable[0];
            if (\Staq\Util::isStack($controller)) {
                $str .= \Staq\Util::getStackSubQuery($controller);
            } else {
                $str .= \UObject::convertToClass($controller);
            }
            $str .= '::' . $this->callable[1];
        } else {
            $str .= 'anonymous';
        }
        return $str . ' )';
    }
}
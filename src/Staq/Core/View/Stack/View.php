<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\View\Stack;

class View extends \Stack\Util\ArrayObject
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $twig;
    protected $debug = FALSE;


    /* CONSTRUCTOR METHODS
     *************************************************************************/
    public function __construct()
    {
        \Twig_Autoloader::register();
        $loader = $this->getTwigEnvironmentLoader();
        $params = $this->getTwigEnvironmentParameters();
        $this->twig = new \Twig_Environment($loader, $params);
        $this->extendTwig();
        $this->initDefaultVariables();
    }

    public function byName($name, $prefix = NULL)
    {
        $class = ['Stack\\View'];
        \UString::doNotStartWith($prefix, ['\\', '_']);
        \UString::doNotEndWith($prefix, ['\\', '_']);
        if (!empty($prefix)) {
            $class[] = $prefix;
        }
        \UString::doNotStartWith($name, ['\\', '_']);
        \UString::doNotEndWith($name, ['\\', '_']);
        if (!empty($name)) {
            $class[] = $name;
        }
        $class = implode('\\', $class);
        return new $class;
    }


    /* PUBLIC METHODS
     *************************************************************************/
    public function render()
    {
        if (!empty($_GET)) {
            $this->entryGet();
        }
        if (!empty($_POST)) {
            $this->entryPost();
        }
        $this->addVariables();
        $template = $this->loadTemplate();
        return $template->render($this->getArrayCopy());
    }

    public function loadTemplate()
    {
        return $this->twig->loadTemplate(static::findTemplate($this));
    }


    /* OVERRIDABLE METHODS
     *************************************************************************/
    protected function entryGet()
    {
    }

    protected function entryPost()
    {
    }

    protected function addVariables()
    {
        $this['UINotification'] = \Stack\Util\UINotification::pull();
    }


    /* STATIC METHODS
     *************************************************************************/
    public static function findTemplate($stack, $action = NULL)
    {
        $template = strtolower(\Staq\Util::getStackSubQuery($stack, '/')) . '.twig';
        $template = str_replace('_', '/', $template);
        if (!empty($action)) {
            $template = $action . '/' . $template;
        }
        $folder = strtolower(\Staq\Util::getStackType($stack));
        while (TRUE) {
            if (\Staq::App()->getFilePath('template/' . $folder . '/' . $template)) {
                break;
            }
            if (\UString::has($template, '/')) {
                $template = \UString::substrBeforeLast($template, '/') . '.twig';
            } else {
                $template = 'index.twig';
                break;
            }
        }
        return $folder . '/' . $template;
    }


    /* PRIVATE METHODS
     *************************************************************************/
    protected function getTwigEnvironmentLoader()
    {
        return new \Twig_Loader_Filesystem(\Staq::App()->getExtensions('template'));
    }

    protected function getTwigEnvironmentParameters()
    {
        $params = [];
        $settings = (new \Stack\Setting)->parse('Application.ini');
        if ($settings->getAsBoolean('cache.twig')) {
            if ($cachePath = \Staq::App()->getPath('cache/twig/', TRUE)) {
                $params['cache'] = $cachePath;
            }
        }
        if ($settings->getAsBoolean('error.display_errors')) {
            $this->debug = TRUE;
            $params['debug'] = TRUE;
        }
        return $params;
    }

    protected function extendTwig()
    {
        $publicFilter = new \Twig_SimpleFilter('public', ['Staq\Util', 'getPublicUrl']);
        $this->twig->addFilter($publicFilter);
        $publicFunction = new \Twig_SimpleFunction('public', ['Staq\Util', 'getPublicUrl']);
        $this->twig->addFunction($publicFunction);
        $assetFunction = new \Twig_SimpleFunction('asset', ['Staq\Util', 'getAssetUrl']);
        $this->twig->addFunction($assetFunction);
        $routeFunction = new \Twig_SimpleFunction('route', ['Staq\Util', 'getControllerUrl']);
        $this->twig->addFunction($routeFunction);
        $routeFunction = new \Twig_SimpleFunction('route_model_*', function($action, $model) {
            return \Staq\Util::getModelControllerUrl($model, $action);
        });
        $this->twig->addFunction($routeFunction);
        $routeFunction = new \Twig_SimpleFunction('route_model', ['Staq\Util', 'getModelControllerUrl']);
        $this->twig->addFunction($routeFunction);
        $findFunction = new \Twig_SimpleFunction('find_template', array(get_class($this), 'findTemplate'));
        $this->twig->addFunction($findFunction);
        if ($this->debug) {
            $this->twig->addExtension(new \Twig_Extension_Debug());
        }
    }

    protected function initDefaultVariables()
    {
    }
}

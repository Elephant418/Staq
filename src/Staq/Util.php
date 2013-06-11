<?php

/* This file is part of the Staq project, which is under MIT license */

namespace Staq;

abstract class Util
{


    /* STRING METHODS
     *************************************************************************/

    // PATH FUNCTIONS
    public static function dirname($path, $level = 1)
    {
        for ($i = 0; $i < $level; $i++) {
            $path = \dirname($path);
        }
        return $path;
    }

    public static function basename($path, $level = 1)
    {
        $dirpath = \Staq\Util::dirname($path, $level);
        return substr($path, strlen($dirpath) + 1);
    }

    public static function getFileExtension($path)
    {
        return \UString::substrAfterLast($path, '.');
    }

    public static function convertPathToNamespace($path)
    {
        return str_replace(DIRECTORY_SEPARATOR, '\\', $path);
    }

    public static function convertNamespaceToPath($namespace)
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
    }


    /* HTTP METHODS
     *************************************************************************/
    public static function httpRedirect($url)
    {
        header('HTTP/1.1 302 Moved Temporarily');
        header('Location: ' . $url);
        die();
    }

    public static function httpRedirectUri($uri)
    {
        \UString::doStartWith($uri, '/');
        \Staq\Util::httpRedirect(\Staq::App()->getBaseUri() . $uri);
    }

    function smartUrlEncode($url)
    {
        $revert = array('%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')');
        return strtr(rawurlencode($url), $revert);
    }


    /* CLI METHODS
     *************************************************************************/
    public static function isCli()
    {
        if (PHP_SAPI !== 'cli') {
            return FALSE;
        }
        global $argv;
        return (!\UString::isEndWith($argv[0], 'phpunit'));
    }


    /* STAQ METHODS
     *************************************************************************/

    // STACK QUERY
    public static function popStackQuery($string)
    {
        if (\Staq\Util::isStackQueryPopable($string)) {
            $string = \UString::substrBeforeLast($string, '\\');
        } else {
            $string = NULL;
        }
        return $string;
    }

    public static function isStackQueryPopable($string)
    {
        return (\UString::has($string, '\\'));
    }

    // STACK OBJECT
    public static function isStackObject($stack)
    {
        return (is_object($stack) && \Staq\Util::isStack($stack));
    }

    public static function isStack($stack, $query = 'Stack\\')
    {
        \UObject::doConvertToClass($stack);
        return \UString::isStartWith($stack, $query);
    }

    public static function getStackQuery($stack)
    {
        \UObject::doConvertToClass($stack);
        if (\Staq\Util::isStack($stack)) {
            return substr($stack, strlen('Stack\\'));
        }
    }

    public static function getStackType($stack)
    {
        $query = static::getStackQuery($stack);
        return \UString::substrBefore($query, '\\');
    }

    public static function getStackSubQuery($stack, $separator = '\\')
    {
        $subQuery = \Staq\Util::getStackQuery($stack);
        \UString::doSubstrAfter($subQuery, '\\');
        return str_replace('\\', $separator, $subQuery);
    }

    public static function getStackSubSubQuery($stack, $separator = '\\')
    {
        $subQuery = \Staq\Util::getStackQuery($stack);
        \UString::doSubstrAfter($subQuery, '\\');
        \UString::doSubstrAfter($subQuery, '\\');
        return str_replace('\\', $separator, $subQuery);
    }

    public static function getStackSubQueryText($stack)
    {
        $subQuery = \Staq\Util::getStackSubQuery($stack);
        return str_replace(['\\', '_'], ' ', $subQuery);
    }

    public static function getStackDefinition($stack)
    {
        if (\Staq\Util::isStack($stack)) {
            $parents = [];
            while ($stack = get_parent_class($stack)) {
                if (
                    \Staq\Util::isStackableClass($stack) &&
                    !\Staq\Util::isParentStack($stack)
                ) {
                    $parents[] = $stack;
                }
            }
            return $parents;
        }
    }

    public static function getStackHeight($stack)
    {
        return count(\Staq\Util::getStackDefinition($stack));
    }

    public static function isStackContains($stack, $class)
    {
        return is_a($stack, $class);
    }

    public static function getStackDebug($stack)
    {
        $list = [];
        foreach (\Staq\Util::getStackDefinition($stack) as $key => $stackable) {
            $debug = [];
            $debug['query'] = \Staq\Util::getStackableQuery($stackable);
            $debug['extension'] = \Staq\Util::getStackableExtension($stackable);
            $list[] = $debug;
        }
        return $list;
    }

    public static function getDeclaredStackClasses()
    {
        $stackClasses = [];
        foreach (get_declared_classes() as $class) {
            if (\Staq\Util::isStack($class)) {
                $stackClasses[] = $class;
            }
        }
        return $stackClasses;
    }

    // STACKABLE CLASS
    public static function isStackableClass($stackable)
    {
        \UObject::doConvertToClass($stackable);
        return (\UString::has($stackable, '\\Stack\\'));
    }

    public static function getStackableExtension($stackable)
    {
        \UObject::doConvertToClass($stackable);
        return (\UString::substrBefore($stackable, '\\Stack\\'));
    }

    public static function getStackableQuery($stackable)
    {
        \UObject::doConvertToClass($stackable);
        return (\UString::substrAfter($stackable, '\\Stack\\'));
    }

    public static function isParentStack($stackable)
    {
        \UObject::doConvertToClass($stackable);
        return (\UString::isEndWith($stackable, '\\__Parent'));
    }

    public static function getParentStackQuery($stackable)
    {
        \UObject::doConvertToClass($stackable);
        $query = \Staq\Util::getStackableQuery($stackable);
        return (\UString::substrBefore($query, '\\__Parent'));
    }


    /* ROUTE METHODS
     *************************************************************************/
    public static function getPublicRoute($path) {
        \UString::doStartWith($path, '/');
        return \Staq::App()->getBaseUri() . $path;
    }

    public static function getAssetRoute($path) {
        $settings = (new \Stack\Setting)->parse('Application.ini');
        if (!$settings->getAsBoolean('cache.asset')) {
            return \Staq\Util::getPublicRoute($path);
        }
        $realPath = \Staq::App()->getFilePath('/public/' . $path);
        if (!$realPath) {
            throw new \Exception('Asset not found: '.$path);
        }
        if (is_dir($realPath)) {
            throw new \Exception('Asset is a directory: '.$path);
        }
        $hash = substr( md5(filemtime($realPath)), 22 );
        $asset = 'asset' . $hash . '/' . $path;
        return \Staq\Util::getPublicRoute($asset);
    }

    public static function getControllerRoute($controller, $action) {
        $parameters = array_slice(func_get_args(), 2);
        $uri = \Staq::App()->getUri($controller, $action, $parameters);
        return \Staq\Util::getPublicRoute($uri);
    }

    public static function getModelControllerRoute($model, $action='view') {
        $controllerName = \Staq\Util::getStackQuery($model);
        $controller = \Staq::Ctrl($controllerName);
        if ($controller) {
            $parameters = $controller->getRouteAttributes($model);
        } else {
            $parameters = [];
        }
        $uri = \Staq::App()->getUri($controller, $action, $parameters);
        return \Staq\Util::getPublicRoute($uri);
    }
}
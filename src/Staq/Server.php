<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

class Server
{


    /* ATTRIBUTES
     *************************************************************************/
    public static $application;
    public static $autoloader;
    protected $applications = [];
    protected $platforms = [];
    public $namespaces = [];


    /* CONSTRUCTOR
     *************************************************************************/
    public function __construct()
    {
        if (!headers_sent() && session_id() === '') {
            session_start();
        }
    }


    /* SETTER METHODS
     *************************************************************************/
    public function addApplication($namespace, $listeningList = NULL)
    {
        if (is_null($listeningList)) {
            $listeningList = $this->getDefaultBaseUri();
        }
        $this->doFormatListeningList($listeningList);
        $this->applications[$namespace] = $listeningList;
        return $this;
    }

    public function addPlatform($platformName, $listeningList = '/')
    {
        $this->doFormatListeningList($listeningList);
        $this->platforms[$platformName] = $listeningList;
        return $this;
    }

    protected function doFormatListeningList(&$listeningList)
    {
        \UArray::doConvertToArray($listeningList);
        $listeningList = (new \Staq\Url)->fromArray($listeningList);
    }


    /* PUBLIC METHODS
     *************************************************************************/
    public function createApplication($namespace = 'Staq\Core\Ground', $baseUri = NULL, $platform = NULL)
    {
        if (empty($baseUri)) {
            $baseUri = $this->getDefaultBaseUri();
        }
        if (empty($platform)) {
            $platform = 'prod';
            if (\Staq\Util::isCli()) {
                if (!isset($argv[1])) {
                    echo 'You must specify a platform.' . PHP_EOL;
                    echo 'Ex: ' . $argv[0] . ' local' . PHP_EOL;
                    die;
                }
                $platform = $argv[1];
            }
        }
        $extensions = $this->findExtensions($namespace);
        if (!is_null(static::$autoloader)) {
            spl_autoload_unregister(array(static::$autoloader, 'autoload'));
        }
        static::$autoloader = new \Staq\Autoloader($extensions);
        spl_autoload_register(array(static::$autoloader, 'autoload'));
        static::$application = new \Stack\Application($extensions, $baseUri, $platform);
        static::$application->initialize();
        return static::$application;
    }

    public function getApp()
    {
        return $this->getCurrentApplication();
    }

    public function getApplication()
    {
        return $this->getCurrentApplication();
    }

    public function getCurrentApplication()
    {
        $this->addDefaultEnvironment();
        $baseUri = '';
        $request = (new \Staq\Url)->byServer();
        $platform = $this->getCurrentPlatform($request, $baseUri);
        $namespace = $this->getCurrentApplicationName($request, $baseUri);
        \UString::doStartWith($baseUri, '/');
        return $this->createApplication($namespace, $baseUri, $platform);
    }


    /* APPLICATION SWITCH SETTINGS
     *************************************************************************/
    protected function getCurrentPlatform($request, &$baseUri)
    {
        foreach ($this->platforms as $platform => $listeningList) {
            foreach ($listeningList as $listening) {
                if ($listening->match($request)) {
                    $baseUri .= $listening->uri;
                    \UString::doNotEndWith($baseUri, '/');
                    return $platform;
                }
            }
        }
    }

    protected function getCurrentApplicationName($request, &$baseUri)
    {
        foreach ($this->applications as $application => $listeningList) {
            foreach ($listeningList as $listening) {
                $listening->uri = $baseUri . $listening->uri;
                if ($listening->match($request)) {
                    $baseUri = $listening->uri;
                    \UString::doNotEndWith($baseUri, '/');
                    return $application;
                }
            }
        }
    }

    protected function getDefaultBaseUri()
    {
        $baseUri = NULL;
        if (isset($_SERVER['DOCUMENT_ROOT']) && isset($_SERVER['SCRIPT_FILENAME'])) {
			$rootFolderPath = \UString::notEndWith($_SERVER['DOCUMENT_ROOT'], '/');
			$scriptFolderPath = dirname($_SERVER['SCRIPT_FILENAME']);
            if (\UString::isStartWith($rootFolderPath, $scriptFolderPath)) {
                $baseUri = \UString::notStartWith($rootFolderPath, $scriptFolderPath);
            }
        }
        if (empty($baseUri)) {
            $baseUri = '/';
        }
        return $baseUri;
    }

    protected function addDefaultEnvironment()
    {
        $this->addApplication('Staq\Core\Ground');
        $this->addPlatform('prod', '/');
    }


    /* EXTENSIONS PARSING SETTINGS
     *************************************************************************/
    protected function findExtensions($namespace)
    {
        $this->initializeNamespaces();
        $files = [];
        $old = [];
        $namespaces = [$namespace, 'Staq\Core\Ground'];
        while (array_diff($namespaces, $old)) {
            $extensions = $this->formatExtensionsFromNamespaces($namespaces);
            foreach ($extensions as $extension) {
                $files[] = $extension . '/setting/Application.ini';
            }
            $ini = (new \Pixel418\Iniliq\IniParser)->parse(array_reverse($files));
            $old = $namespaces;
            $namespaces = array_reverse($ini->getAsArray('extension.list'));
            $namespaces = \UArray::reverseMergeUnique($old, $namespaces);
        }
        return $this->formatExtensionsFromNamespaces($namespaces);
    }

    protected function initializeNamespaces()
    {
        if (empty($this->namespaces)) {
			$vendorPath = DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR;
            if (\UString::has(__DIR__, $vendorPath)) {
                $baseDir = \UString::substrBeforeLast(__DIR__, $vendorPath);
            } else {
                $baseDir = __DIR__ . '/../..';
            }
            $psr0 = (require($baseDir . '/vendor/composer/autoload_namespaces.php'));
            foreach ($psr0 as $namespace => $pathList) {
                foreach ($pathList as $key => $path) {
                    $psr0[$namespace][$key] = realpath($path.'/'.str_replace('\\', '/', $namespace));
                }
            }
            $psr4 = (require($baseDir . '/vendor/composer/autoload_psr4.php'));
            foreach ($psr4 as $namespace => $pathList) {
                foreach ($pathList as $key => $path) {
                    $psr0[$namespace][$key] = realpath($path);
                }
            }
            $this->namespaces = array_merge($psr0, $psr4);
        }
    }

    protected function formatExtensionsFromNamespaces($extensions)
    {
        \UArray::doConvertToArray($extensions);
        foreach ($extensions as $key => $namespace) {
            $this->doFormatExtensionNamespace($namespace);
            $path = $this->findExtensionPath($namespace);
            unset($extensions[$key]);
            if (!empty($path)) {
                $extensions[$namespace] = $path;
            }
        }
        return $extensions;
    }

    protected function findExtensionPath($namespace)
    {
        foreach ($this->namespaces as $baseNamespace => $basePathList) {
            if (\UString::isStartWith($namespace, $baseNamespace)) {
                \UArray::doConvertToArray($basePathList);
                foreach ($basePathList as $basePath) {
                    \UString::doEndWith($basePath, DIRECTORY_SEPARATOR);
                    \UString::doNotStartWith($namespace, $baseNamespace);
                    \UString::doNotStartWith($namespace, '\\');
                    $path = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
                    $path = $basePath . $path;
                    if (is_dir($path)) {
                        return $path;
                    }
                }
            }
        }
    }

    protected function doFormatExtensionNamespace(&$namespace)
    {
        $namespace = str_replace(DIRECTORY_SEPARATOR, '\\', $namespace);
        \UString::doNotStartWith($namespace, '\\');
        \UString::doNotEndWith($namespace, '\\');
    }
}

if (!defined('HTML_EOL')) {
    define('HTML_EOL', '<br/>' . PHP_EOL);
}

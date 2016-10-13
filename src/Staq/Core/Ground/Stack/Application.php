<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Ground\Stack;

class Application
{


    /* ATTRIBUTES
     *************************************************************************/
    public $settings;
    protected $extensions;
    protected $baseUri;
    protected $platform;
    protected $initialized = FALSE;


    /* GETTER
     *************************************************************************/
    public function getController($name)
    {
        throw new \Exception('There is no controller without Router. Please add the Staq/Core/Router extension.');
    }

    public function getExtensions($file = NULL)
    {
        $extensions = $this->extensions;
        if (!empty($file)) {
            \UString::doStartWith($file, DIRECTORY_SEPARATOR);
            array_walk($extensions, function (&$a) use ($file) {
                $a = realpath($a . $file);
            });
            $extensions = array_filter($extensions, function ($a) {
                return ($a !== FALSE);
            });
        }
        return $extensions;
    }

    public function getFilePath($file = NULL)
    {
        $paths = $this->getExtensions($file);
        if (!empty($paths)) {
            return reset($paths);
        }
    }

    public function getDataPath($file = NULL, $create = FALSE)
    {
        if (isset($this->settings['extension']['data'])) {
            $dataNamespace = $this->settings['extension']['data'];
            if (isset($this->extensions[$dataNamespace])) {
                return $this->extensions[$dataNamespace];
            }
        }
        return $this->getFilePath($file);
    }

    public function getExtensionNamespaces()
    {
        return array_keys($this->extensions);
    }

    public function getNamespace()
    {
        $extensions = $this->getExtensionNamespaces();
        return reset($extensions);
    }

    public function getPath($file = NULL, $create = FALSE)
    {
        $path = reset($this->extensions);
        if (!empty($file)) {
            \UString::doStartWith($file, DIRECTORY_SEPARATOR);
            $path .= $file;
            $realPath = realpath($path);
            if ($realPath == FALSE && $create) {
                if (@mkdir($path, 0755, TRUE)) {
                    $realPath = realpath($path);
                }
            }
            $path = $realPath;
        }
        return $path;
    }

    public function getBaseUri()
    {
        return $this->baseUri;
    }

    public function getPlatform()
    {
        return $this->platform;
    }

    public function isInitialized()
    {
        return $this->initialized;
    }


    /* SETTER
     *************************************************************************/
    public function setPlatform($platform)
    {
        $this->platform = $platform;
        $this->initialize();
        return $this;
    }

    public function setBaseUri($baseUri)
    {
        \UString::doStartWith($baseUri, '/');
        \UString::doNotEndWith($baseUri, '/');
        $this->baseUri = $baseUri;
        return $this;
    }


    /* INITIALIZATION
     *************************************************************************/
    public function __construct($extensions, $baseUri, $platform)
    {
        $this->extensions = $extensions;
        $this->setBaseUri($baseUri);
        $this->platform = $platform;
    }

    public function initialize()
    {
        $this->settings = (new \Stack\Setting)
            ->clearCache()
            ->parse($this);

        // Display errors
        $displayErrors = 0;
        if ($this->settings->getAsBoolean('error.display_errors') || \Staq\Util::isCli()) {
            $displayErrors = 1;
        }
        ini_set('display_errors', $displayErrors);

        // Level reporting
        if (\Staq\Util::isCli()) {
            $level = E_ALL & ~(E_STRICT);
        } else {
            $level = $this->settings->get('error.error_reporting');
            if (!is_numeric($level)) {
                $level = 0;
            }
        }
        error_reporting($level);

        // Hide uncompatible method declaration
        if (PHP_MAJOR_VERSION >= 7) {
            set_error_handler(function ($errno, $errstr) {
                return strpos($errstr, 'Declaration of') === 0;
            }, E_WARNING);
        }

        // Timezone
        $timezone = $this->settings->get('service.timezone');
        date_default_timezone_set($timezone);

        $this->initialized = TRUE;
        return $this;
    }
}

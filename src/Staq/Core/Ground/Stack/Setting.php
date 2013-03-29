<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Ground\Stack;

class Setting
{


    /*************************************************************************
    ATTRIBUTES
     *************************************************************************/
    static public $initialized = FALSE;
    static public $cache = [];
    static public $cacheFile;


    /*************************************************************************
    CONSTRUCTOR
     *************************************************************************/
    public function __construct()
    {
        if (!static::$initialized) {
            $this->initialize();
        }
    }

    protected function initialize()
    {
        if ($path = \Staq::App()->getPath('cache/', TRUE)) {
            static::$cacheFile = $path . '/setting.' . \Staq::App()->getPlatform() . '.php';
            if (is_file(static::$cacheFile)) {
                require(static::$cacheFile);
                if (is_array($cache)) {
                    static::$cache = $cache;
                }
            }
        }
        static::$initialized = TRUE;
        return $this;
    }


    /*************************************************************************
    CACHE METHODS
     *************************************************************************/
    public function clearCache()
    {
        static::$cache = [];
        static::$cacheFile = NULL;
        $this->initialize();
        return $this;
    }

    protected function hasCache($settingFileName)
    {
        return isset(static::$cache[$settingFileName]);
    }

    protected function addCache($settingFileName, $settings)
    {
        $settings = $settings->getArrayCopy();
        static::$cache[$settingFileName] = $settings;
        if (\Staq::App()->isInitialized()) {
            $setting = (new $this)->parse('Application');
            if ($setting->getAsBoolean('cache.setting')) {
                if (
                    !static::$cacheFile ||
                    !$handle = @fopen(static::$cacheFile, 'a')
                ) {
                    return NULL;
                }
                if (0 == filesize(static::$cacheFile)) {
                    fwrite($handle, '<?php' . PHP_EOL . '$cache = array( );' . PHP_EOL);
                }
                fwrite($handle, '$cache["' . $settingFileName . '"] = ' . var_export($settings, TRUE) . ';' . PHP_EOL);
                fclose($handle);
            }
        }
    }

    protected function getCache($settingFileName)
    {
        return new \Stack\Util\ArrayObject(static::$cache[$settingFileName]);
    }


    /*************************************************************************
    PARSE METHODS
     *************************************************************************/
    public function parse($mixed)
    {
        if (\Staq\Util::isStack($mixed)) {
            return $this->parseFromStack($mixed);
        }
        return $this->parseFromString($mixed);
    }

    protected function parseFromStack($stack)
    {
        $settingFileName = $this->getSettingFileNameFromStack($stack);
        return $this->parseFromString($settingFileName);
    }

    protected function parseFromString($settingFileName)
    {
        \UString::doSubstrBefore($settingFileName, '.');
        if (!$this->hasCache($settingFileName)) {
            $filePaths = $this->getFilePaths($settingFileName);
            $stack = 'Stack\\' . \Staq\Util::convertPathToNamespace($settingFileName);
            foreach (\Staq\Util::getStackDefinition($stack) as $class) {
                if (isset($class::$setting)) {
                    array_unshift($filePaths, $class::$setting);
                }
            }
            $settings = (new \Stack\Util\IniParser)->parse($filePaths);
            $this->addCache($settingFileName, $settings);
        }
        return $this->getCache($settingFileName);
    }

    protected function getSettingFileNameFromStack($stack)
    {
        $settingFileName = \Staq\Util::getStackQuery($stack);
        return \Staq\Util::convertNamespaceToPath($settingFileName);
    }

    protected function getFilePaths($fullSettingFileName)
    {
        $fileNames = $this->getFileNames($fullSettingFileName);
        $platformName = \Staq::App()->getPlatform();
        $filePaths = [];
        foreach (\Staq::App()->getExtensions() as $extension) {
            foreach ($fileNames as $fileName) {
                if ($platformName) {
                    $fileName .= '.' . $platformName;
                }
                while ($fileName) {
                    $path = realpath($extension . '/setting/' . $fileName . '.ini');
                    if ($path) {
                        $filePaths[] = $path;
                    }
                    $fileName = \UString::substrBeforeLast($fileName, '.');
                }
            }
        }
        return array_reverse($filePaths);
    }

    protected function getFileNames($fileName)
    {
        $fileNames = [];
        do {
            $fileNames[] = $fileName;
            $fileName = \UString::substrBeforeLast($fileName, '/');
        } while (!empty($fileName));
        return $fileNames;
    }
}

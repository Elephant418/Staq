<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

class Autoloader
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $extensions = [];
    static public $initialized = FALSE;
    static public $cacheFile;


    /* INITIALIZATION
     *************************************************************************/
    public function __construct($extensions)
    {
        $this->extensions = $extensions;
    }

    protected function initialize()
    {
        if (\Staq::App() && \Staq::App()->isInitialized()) {
            static::$cacheFile = reset($this->extensions) . '/cache/autoload.php';
            if (\is_file(static::$cacheFile)) {
                require_once(static::$cacheFile);
            }
            static::$initialized = TRUE;
        }
        return $this;
    }


    /* TOP-LEVEL AUTOLOAD
     *************************************************************************/
    public function autoload($class)
    {
        if (!static::$initialized) {
            $this->initialize();
            if ($this->classExists($class)) {
                return TRUE;
            }
        }
        if (\Staq\Util::isStack($class)) {
            $this->loadStackClass($class);
        } else if (\Staq\Util::isParentStack($class)) {
            $this->loadStackParentClass($class);
        }
    }


    /* FILE CLASS MANAGEMENT
     *************************************************************************/
    protected function loadStackClass($class)
    {
        $stackQuery = \Staq\Util::getStackQuery($class);
        while ($stackQuery) {
            $relativePath = \Staq\Util::getStackFileFromQuery($stackQuery);
            foreach (array_keys($this->extensions) as $extensionNamespace) {
                if ($this->isClassExist($relativePath, $extensionNamespace)) {
                    $realClass = $this->getRealClass($stackQuery, $extensionNamespace);
                    $this->createClassAlias($class, $realClass);
                    return TRUE;
                }
            }
            $stackQuery = \Staq\Util::popStackQuery($stackQuery);
        }

        $this->createClassEmpty($class);
    }

    protected function getRealClass($stack, $extensionNamespace)
    {        
        return $extensionNamespace . '\\Stack\\' . \Staq\Util::getStackClassFromQuery($stack);
    }

    // "stack" is now a part of the namespace, there is no burgers left at my bakery
    protected function isClassExist($relativePath, $extensionNamespace)
    {
        $absolutePath = $this->extensions[$extensionNamespace] . '/Stack/' . $relativePath;
        $classExist = !! realpath($absolutePath);
        
        return $classExist;
    }

    protected function loadStackParentClass($class)
    {
        $queryExtension = \Staq\Util::getStackableExtension($class);
        $stackQuery = \Staq\Util::getParentStackQuery($class);
        $ready = FALSE;
        while ($stackQuery) {
            $relativePath = \Staq\Util::convertNamespaceToPath($stackQuery).'.php';
            foreach (array_keys($this->extensions) as $extensionNamespace) {
                if ($ready) {
                    if ($this->isClassExist($relativePath, $extensionNamespace)) {
                        $realClass = $extensionNamespace.'\\Stack\\'.$stackQuery;
                        $this->createClassAlias($class, $realClass);
                        return TRUE;
                    }
                } else {
                    if ($queryExtension === $extensionNamespace) {
                        $ready = TRUE;
                    }
                }
            }
            $stackQuery = \Staq\Util::popStackQuery($stackQuery);
            $ready = TRUE;
        }

        $this->createClassEmpty($class);
    }


    /* CLASS DECLARATION
     *************************************************************************/
    protected function classExists($class)
    {
        return (\class_exists($class) || \interface_exists($class));
    }

    protected function createClassAlias($alias, $class)
    {
        return $this->createClass($alias, $class, \interface_exists($class));
    }

    protected function createClassEmpty($class)
    {
        return $this->createClass($class, NULL);
    }

    protected function createClass($class, $baseClass, $isInterface = FALSE)
    {
        $namespace = \UObject::getNamespace($class, '\\');
        $name = \UObject::getClassName($class, '\\');
        $code = '';
        if ($namespace) {
            $code = 'namespace ' . $namespace . ' {' . PHP_EOL;
        }
        if ($isInterface) {
            $code .= 'interface';
        } else {
            $code .= 'class';
        }
        $code .= ' ' . $name . ' ';
        if ($baseClass) {
            $code .= 'extends \\' . $baseClass . ' ';
        }
        $code .= '{ }' . PHP_EOL . '}' . PHP_EOL;
        $this->addToCache($code);
        eval($code);
        
        return true;
    }

    protected function addToCache($code)
    {
        if (
            !static::$initialized ||
            !static::$cacheFile ||
            !(new \Stack\Setting)
                ->parse('Application')
                ->getAsBoolean('cache.autoload') ||
            !$handle = @fopen(static::$cacheFile, 'a')
        ) {
            return NULL;
        }
        fwrite($handle, '<?php ' . $code . ' ?>' . PHP_EOL);
        fclose($handle);
    }
}
<?php

namespace Test\Staq;

$autoload = '/../../../vendor/autoload.php';
if (is_file(__DIR__ . $autoload)) {
    require_once(__DIR__ . $autoload);
} else {
    require_once(__DIR__ . '/../../../' . $autoload);
}

class StaqTestCase extends \PHPUnit_Framework_TestCase
{


    /* ATTRIBUTES
     *************************************************************************/
    public $projectNamespace = 'Test\\Staq\\Project\\';


    /* CONSTRUCTOR
     *************************************************************************/
    public function __construct()
    {

        // Initialize project namespace
        $projectName = \UObject::getClassName($this);
        \UString::doNotEndWith($projectName, 'Test');
        $this->projectNamespace .= $projectName;
    }


    /* UTIL METHODS
     *************************************************************************/
    public function getProjectClass($name)
    {
        return $this->projectNamespace . '\\' . $name;
    }

    public function getProjectStackClass($name)
    {
        return $this->getProjectClass('Stack\\' . $name);
    }
}
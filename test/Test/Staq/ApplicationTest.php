<?php

namespace Test\Staq;

$autoload = '/../../../vendor/autoload.php';
if (is_file(__DIR__ . $autoload)) {
    require_once(__DIR__ . $autoload);
} else {
    require_once(__DIR__ . '/../../../' . $autoload);
}

echo 'Staq ' . \Staq::VERSION . ' tested with ';

class ApplicationTest extends StaqTestCase
{


    /* ATTRIBUTES
     *************************************************************************/
    public $starterNamespaces = ['Staq\\App\\Starter', 'Staq\\Core\\View', 'Staq\\Core\\Router', 'Staq\\Core\\Ground', 'Pixel418\\Iniliq'];


    /* UTIL METHODS
     *************************************************************************/
    public function appendProjectNamespace($name)
    {
        $names = array_map([$this, 'getProjectClass'], func_get_args());
        return array_merge($names, $this->starterNamespaces);
    }


    /* EXTENSIONS TEST METHODS
     *************************************************************************/
    public function test_empty_project__extensions()
    {
        $app = \Staq\App::create()
            ->setPlatform('local');
        $this->assertEquals($this->starterNamespaces, $app->getExtensionNamespaces());
    }

    public function test_empty_project__platform__default()
    {
        $app = \Staq\App::create();
        $this->assertEquals('prod', $app->getPlatform());
    }

    public function test_empty_project__platform__setted()
    {
        $app = \Staq\App::create()
            ->setPlatform('local');
        $this->assertEquals('local', $app->getPlatform());
    }

    public function test_no_configuration__extensions()
    {
        $projectNamespace = $this->getProjectClass('NoConfiguration');
        $app = \Staq\App::create($projectNamespace)
            ->setPlatform('local');
        $expected = $this->appendProjectNamespace('NoConfiguration');
        $this->assertEquals($expected, $app->getExtensionNamespaces());
    }

    public function test_simple_configuration__extensions()
    {
        $projectNamespace = $this->getProjectClass('SimpleConfiguration');
        $app = \Staq\App::create($projectNamespace)
            ->setPlatform('local');
        $expected = $this->appendProjectNamespace('SimpleConfiguration');
        $this->assertEquals($expected, $app->getExtensionNamespaces());
    }

    public function test_extend_no_configuration__extensions()
    {
        $projectNamespace = $this->getProjectClass('ExtendNoConfiguration');
        $app = \Staq\App::create($projectNamespace)
            ->setPlatform('local');
        $expected = $this->appendProjectNamespace('ExtendNoConfiguration', 'NoConfiguration');
        $this->assertEquals($expected, $app->getExtensionNamespaces());
    }

    public function test_without_starter__extensions()
    {
        $projectNamespace = $this->getProjectClass('WithoutStarter');
        $app = \Staq\App::create($projectNamespace)
            ->setPlatform('local');
        $expected = $this->appendProjectNamespace('WithoutStarter');
        \UArray::doRemoveValue($expected, 'Staq\\App\\Starter');
        $this->assertEquals($expected, $app->getExtensionNamespaces());
    }


    /* EXTENSIONS TEST METHODS
     *************************************************************************/
    public function test_error_reporting__none()
    {
        $app = \Staq\App::create();
        $this->assertEquals(0, ini_get('display_errors'));
    }

    public function test_error_reporting__display()
    {
        $this->setExpectedException('PHPUnit_Framework_Error');
        $app = \Staq\App::create()
            ->setPlatform('local');
        $this->assertEquals(1, ini_get('display_errors'));
        trigger_error('Test of warnings', E_USER_ERROR);
    }
}
<?php

namespace Test\Staq;

$autoload = '/../../../vendor/autoload.php';
if (is_file(__DIR__ . $autoload)) {
    require_once(__DIR__ . $autoload);
} else {
    require_once(__DIR__ . '/../../../' . $autoload);
}

class AutoloaderTest extends StaqTestCase
{


    /*************************************************************************
    TEST METHODS
     *************************************************************************/
    public function test_unexisting_class__simple()
    {
        $app = \Staq\App::create()
            ->setPlatform('local');
        $stack = new \Stack\Unexisting\Coco;
        $this->assertEquals('Stack\\Unexisting\\Coco', get_class($stack));
        $this->assertEquals(0, \Staq\Util::getStackHeight($stack));
    }

    public function test_unexisting_class__complex()
    {
        $app = \Staq\App::create()
            ->setPlatform('local');
        $stack = new \Stack\Unexisting\Coco\Des\Bois;
        $this->assertEquals('Stack\\Unexisting\\Coco\\Des\\Bois', get_class($stack));
        $this->assertEquals(0, \Staq\Util::getStackHeight($stack));
    }

    public function test_existing_class__simple()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Existing\Coco;
        $this->assertEquals(1, \Staq\Util::getStackHeight($stack));
        $this->assertTrue(is_a($stack, $this->getProjectStackClass('Existing\\Coco')));
    }

    public function test_existing_class__complex()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Existing\Coco\Des\Bois;
        $this->assertEquals(1, \Staq\Util::getStackHeight($stack));
        $this->assertTrue(is_a($stack, $this->getProjectStackClass('Existing\\Coco')));
    }

    public function test_controller_class__unexisting()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Controller\Unexisting;
        $this->assertTrue(is_a($stack, 'Staq\Core\Router\Stack\Controller'));
    }

    public function test_controller_class__existing()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Controller\Existing\Coco;
        $this->assertTrue(is_a($stack, $this->getProjectStackClass('Controller\\Existing\\Coco')));
    }

    public function test_controller_class__extending()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Controller\Existing\Coco;
        $this->assertTrue(is_a($stack, 'Staq\Core\Router\Stack\Controller'));
    }

    public function test_exception_class__existing()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Exception;
        $this->assertTrue(is_a($stack, 'Staq\Core\Ground\Stack\Exception'));
    }

    public function test_exception_class__extending()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Exception;
        $this->assertTrue(is_a($stack, 'Exception'));
    }
}
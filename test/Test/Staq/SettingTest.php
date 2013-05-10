<?php

namespace Test\Staq;

$autoload = '/../../../vendor/autoload.php';
if (is_file(__DIR__ . $autoload)) {
    require_once(__DIR__ . $autoload);
} else {
    require_once(__DIR__ . '/../../../' . $autoload);
}

class SettingTest extends StaqTestCase
{


    /* TEST METHODS
     *************************************************************************/
    public function test_unexisting_setting()
    {
        $app = \Staq\App::create($this->projectNamespace);
        $setting = (new \Stack\Setting)->parse('Test');
        $this->assertEquals('a_value', $setting['test.a_setting']);
    }

    public function test_existing_setting__existing_key()
    {
        $app = \Staq\App::create($this->projectNamespace);
        $setting = (new \Stack\Setting)->parse('Application');
        $this->assertEquals(0, $setting['error.display_errors']);
    }

    public function test_existing_setting__custom_key()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $setting = (new \Stack\Setting)->parse('Application');
        $this->assertEquals('a_value', $setting['error.a_setting']);
    }

    public function test_existing_setting__inherit_key__extension()
    {
        $app = \Staq\App::create($this->projectNamespace);
        $setting = (new \Stack\Setting)->parse('Application');
        $this->assertEquals('E_STRICT', $setting['error.error_reporting']);
    }

    public function test_existing_setting__inherit_key__platform()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $setting = (new \Stack\Setting)->parse('Application');
        $this->assertEquals(1, $setting['error.display_errors']);
    }

    public function test_existing_setting__merged_key__platform()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $setting = (new \Stack\Setting)->parse('Test');
        $this->assertEquals(['a_value', 'more_value'], $setting['test.a_setting']);
    }

    public function test_stack_setting_file()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Controller;
        $setting = (new \Stack\Setting)->parse($stack);
        $this->assertEquals('empty', $setting['view.layout']);
    }

    public function test_stack_setting_file__complex()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Controller\Unexisting;
        $setting = (new \Stack\Setting)->parse($stack);
        $this->assertEquals('bootstrap', $setting['view.layout']);
    }

    public function test_stack_setting_file__inherit_class_name()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Controller\Unexisting;
        $setting = (new \Stack\Setting)->parse($stack);
        $this->assertEquals('coco', $setting['view.title']);
    }

    public function test_stack_setting_attribute()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Setting\Coco;
        $setting = (new \Stack\Setting)->parse($stack);
        $this->assertEquals(['one'], $setting['data.value.list']);
    }

    public function test_stack_setting_attribute__complex()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Setting\Coco\Des;
        $setting = (new \Stack\Setting)->parse($stack);
        $this->assertEquals(['one', 'two'], $setting['data.value.list']);
    }

    public function test_stack_setting_attribute__overrided_file()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
        $stack = new \Stack\Setting\Coco\Des\Bois;
        $setting = (new \Stack\Setting)->parse($stack);
        $this->assertEquals(['three', 'four'], $setting['data.value.list']);
    }
}
<?php

namespace Test\Staq;

$autoload = '/../../../vendor/autoload.php';
if (is_file(__DIR__ . $autoload)) {
    require_once(__DIR__ . $autoload);
} else {
    require_once(__DIR__ . '/../../../' . $autoload);
}

class DataFileTest extends StaqTestCase
{


    /* GLOBAL METHODS
     *************************************************************************/
    protected function setUp()
    {
        $app = \Staq\App::create($this->projectNamespace)
            ->setPlatform('local');
    }

    protected function tearDown()
    {
    }


    /* VARCHAR SCUD TEST METHODS
     *************************************************************************/
    public function test_select__no_match()
    {
        $page = (new \Stack\Entity\Page)->fetchById(1664);
        $this->assertFalse($page->exists());
    }

    public function testInsertionAndDeletion_EmptyPage()
    {
        $page = new \Stack\Model\Page;
        $this->assertFalse($page->exists());
        $page['name'] = 'Empty page';
        $page->save();
        $this->assertTrue($page->exists());
        $id = $page->id;
        unset($page);
        $page = (new \Stack\Entity\Page)->fetchById($id);
        $this->assertTrue($page->exists());
        $this->assertEquals('Empty page', $page['name']);
        $page->delete();
        $this->assertFalse($page->exists());
        unset($page);
        $page = (new \Stack\Entity\Page)->fetchById($id);
        $this->assertFalse($page->exists());
        $this->assertNull($page['name']);
    }

    public function testInsertionAndDeletion_HTMLPage()
    {
        $page = new \Stack\Model\Page;
        $this->assertFalse($page->exists());
        $page['name'] = 'html page';
        $page['content'] = '<h1>Coco</h1>'.PHP_EOL;
        $page->save();
        $this->assertTrue($page->exists());
        $id = $page->id;
        unset($page);
        $page = (new \Stack\Entity\Page)->fetchById($id);
        $this->assertTrue($page->exists());
        $this->assertEquals('<h1>Coco</h1>'.PHP_EOL, $page['content']);
        $page->delete();
        $this->assertFalse($page->exists());
        unset($page);
        $page = (new \Stack\Entity\Page)->fetchById($id);
        $this->assertFalse($page->exists());
        $this->assertNull($page['content']);
    }
}
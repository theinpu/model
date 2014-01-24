<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 16:17
 */

namespace bc\tests;


use bc\pdo\PDOHelper;
use bc\tests\dummy\DummyBuilder;
use bc\tests\dummy\DummyDataMap;
use bc\tests\dummy\DummyFactory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DummyFactory
     */
    private $factory;

    public function testCreate() {
        $item = DummyBuilder::create()->title('test')->build();
        $this->factory->save($item);
        $this->assertEquals(1, $item->getId());
    }

    public function testGetItem() {
        $this->factory->save(DummyBuilder::create()->title('test')->build());
        $item = $this->factory->get(1);
        $this->assertInstanceOf('bc\\tests\\dummy\\DummyModel', $item);
        $this->assertEquals(1, $item->getId());
    }

    public function testGetAll() {
        $this->factory->save(DummyBuilder::create()->title('title 1')->build());
        $this->factory->save(DummyBuilder::create()->title('title 2')->build());
        $items = $this->factory->getAll();
        $this->assertCount(2, $items);
    }

    public function testGetList() {
        $this->factory->save(DummyBuilder::create()->title('title 1')->build());
        $this->factory->save(DummyBuilder::create()->title('title 2')->build());
        $this->factory->save(DummyBuilder::create()->title('title 3')->build());

        $this->assertCount(2, $this->factory->getList(array(1, 3)));
    }

    public function testGetPartial() {
        $this->factory->save(DummyBuilder::create()->title('title 1')->build());
        $this->factory->save(DummyBuilder::create()->title('title 2')->build());
        $this->factory->save(DummyBuilder::create()->title('title 3')->build());

        $this->assertCount(2, $this->factory->getPartial(0, 2));
    }

    public function testCount() {
        $this->factory->save(DummyBuilder::create()->title('title 1')->build());
        $this->factory->save(DummyBuilder::create()->title('title 2')->build());
        $this->factory->save(DummyBuilder::create()->title('title 3')->build());

        $this->assertEquals(3, $this->factory->count());
    }

    public function testDelete() {
        $this->factory->save(DummyBuilder::create()->title('title 1')->build());
        $this->factory->save(DummyBuilder::create()->title('title 2')->build());
        $this->factory->save(DummyBuilder::create()->title('title 3')->build());

        $this->factory->delete(2);
        $this->assertEquals(2, $this->factory->count());
    }

    protected function setUp() {
        $this->factory = new DummyFactory(new DummyDataMap());
    }

    protected function tearDown() {
        PDOHelper::getPDO()->query("TRUNCATE test_table");
    }

}
 
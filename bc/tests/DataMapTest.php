<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 15:17
 */

namespace bc\tests;

use bc\pdo\PDOHelper;
use bc\tests\dummy\DummyDataMap;
use bc\tests\dummy\DummyModel;

class DataMapTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DummyDataMap
     */
    private $dm;

    public function testCreateItem() {
        $this->dm = new DummyDataMap();
        $this->assertInstanceOf('bc\\model\\DataMap', $this->dm);
        $item = new DummyModel();
        $item->setTitle('test');
        $this->dm->save($item);
        $this->assertEquals(1, $item->getId());
        $item->setTitle('updated');
        $this->dm->save($item);
        $savedItem = $this->dm->get($item->getId());
        $this->assertEquals(array($item), array($savedItem));
    }

    public function testGetAll() {
        $items = $this->fillItems();
        $savedItems = $this->dm->getAll();
        $this->assertEquals($items, $savedItems);
    }

    public function testGetList() {
        $items = $this->fillItems();
        $itemList = $this->dm->getList(array(1, 3, 4));
        $this->assertCount(3, $itemList);
        $this->assertEquals(array($items[0], $items[2], $items[3]), $itemList);
        $itemList = $this->dm->getList(1);
        $this->assertCount(1, $itemList);
        $this->assertEquals(array($items[0]), $itemList);
        $itemList = $this->dm->getList(100);
        $this->assertCount(0, $itemList);
    }

    public function testGetPartial() {
        $items = $this->fillItems();
        $findItem = $this->dm->getPartial(0, 5);
        $this->assertCount(5, $findItem);
        $this->assertEquals(array(
            $items[0], $items[1], $items[2], $items[3], $items[4]
        ), $findItem);

        $findItem = $this->dm->getPartial(5, 5);
        $this->assertCount(5, $findItem);

        $this->assertEquals(array(
            $items[5], $items[6], $items[7], $items[8], $items[9]
        ), $findItem);

        $findItem = $this->dm->getPartial(0, 0);
        $this->assertCount(1, $findItem);
        $this->assertEquals(array($items[0]), $findItem);

        $findItem = $this->dm->getPartial(15, -1);
        $this->assertCount(0, $findItem);
    }

    public function testCount() {
        $this->assertEquals(0, $this->dm->count());
        $this->fillItems();
        $this->assertEquals(10, $this->dm->count());
    }

    public function testDelete() {
        $this->fillItems();
        $this->dm->delete(1);
        $items = $this->dm->getAll();
        $this->assertCount(9, $items);
    }

    public function testFetchAsArray() {
        $items = $this->fillItems();
        $itemsArray = array();
        foreach ($items as $item) {
            $itemsArray[] = array(
                'id'    => $item->getId(),
                'title' => $item->getTitle()
            );
        }
        $fetchedItems = $this->dm->fetchAllAsArray();
        $this->assertEquals($itemsArray, $fetchedItems);
    }

    protected function setUp() {
        $this->dm = new DummyDataMap();
    }

    protected function tearDown() {
        PDOHelper::getPDO()->query("TRUNCATE test_table");
    }

    /**
     * @return dummy\DummyModel[]
     */
    private function fillItems() {
        /** @var DummyModel[] $items */
        $items = array();
        for ($i = 0; $i < 10; $i++) {
            $items[$i] = new DummyModel();
            $items[$i]->setTitle('title ' . $i);
            $this->dm->save($items[$i]);
        }
        return $items;
    }

}
 
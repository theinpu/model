<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 16:12
 */

namespace bc\tests;


use bc\tests\dummy\DummyBuilder;

class BuilderTest extends \PHPUnit_Framework_TestCase {

    public function testCreate() {
        $item = DummyBuilder::create()
                            ->title('test')
                            ->build();

        $this->assertInstanceOf('bc\\tests\\dummy\\DummyModel', $item);
        $this->assertEquals('test', $item->getTitle());
        $this->assertEquals(
             json_encode(array('id' => null, 'title' => 'test')),
             $item->getJSON()
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyTitle() {
        DummyBuilder::create()->build();
    }

}
 
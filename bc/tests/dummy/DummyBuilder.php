<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 16:09
 */

namespace bc\tests\dummy;

use bc\model\IBuilder;

class DummyBuilder implements IBuilder {

    private $title = null;

    public static function create() {
        return new self();
    }

    /**
     * @throws \InvalidArgumentException
     * @return DummyModel
     */
    public function build() {
        if(empty($this->title)) {
            throw new \InvalidArgumentException("Need to set title");
        }
        $item = new DummyModel();
        $item->setTitle($this->title);
        return $item;
    }

    /**
     * @param $title
     * @return DummyBuilder
     */
    public function title($title) {
        $this->title = $title;
        return $this;
    }
}
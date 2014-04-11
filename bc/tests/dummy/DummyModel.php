<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 14:06
 */

namespace bc\tests\dummy;

use bc\model\JSONExport;
use bc\model\Model;

class DummyModel extends Model implements JSONExport {

    private $title;

    /**
     * @param mixed $title
     */
    public function setTitle($title) {
        $this->title = $title;
        $this->changed();
    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getJSON() {
        return json_encode($this->getArray());
    }

    /**
     * @return array
     */
    public function getArray() {
        return array(
            'id'    => $this->getId(),
            'title' => $this->getTitle(),
        );
    }
}
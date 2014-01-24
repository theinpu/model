<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 14:06
 */

namespace bc\tests\dummy;

use bc\model\Model;

class DummyModel extends Model {

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

} 
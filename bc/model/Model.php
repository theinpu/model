<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 13:50
 */

namespace bc\model;

abstract class Model {

    /**
     * @var int
     */
    private $id;
    private $changed = false;

    /**
     * Called on save object
     */
    public function onSave() {
        $this->changed = false;
    }

    public function isChanged() {
        return $this->changed;
    }

    protected function changed() {
        if (!is_null($this->getId())) {
            $this->changed = true;
            $this->onChanged();
        }
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        if (!is_null($this->id)) {
            throw new \RuntimeException("You not allow to change " . get_class($this) . '::$id');
        }
        $this->id = $id;
    }

    public function onCreate() {}

    public function onLoad() {}

    public function convert() {
        return $this->getId();
    }

    protected function onChanged() {}
} 
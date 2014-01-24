<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 13:58
 */

namespace bc\model;

abstract class Factory {

    /**
     * @var DataMap
     */
    private $dataMap = null;

    public function __construct(DataMap $dataMap) {
        $this->dataMap = $dataMap;
    }

    /**
     * @param int $id
     * @return Model
     */
    public function get($id) {
        return $this->getDataMap()->get($id);
    }

    /**
     * @return Model[]
     */
    public function getAll() {
        return $this->getDataMap()->getAll();
    }

    /**
     * @param array $ids
     * @return Model[]
     */
    public function getList($ids) {
        return $this->getDataMap()->getList($ids);
    }

    /**
     * @param int $offset
     * @param int $count
     * @return Model[]
     */
    public function getPartial($offset, $count) {
        return $this->getDataMap()->getPartial($offset, $count);
    }

    /**
     * @return int
     */
    public function count() {
        return $this->getDataMap()->count();
    }

    /**
     * @param Model $item
     * @return void
     */
    public function save(Model $item) {
        $this->getDataMap()->save($item);
    }

    /**
     * @param $id
     * @return void
     */
    public function delete($id) {
        $this->getDataMap()->delete($id);
    }

    /**
     * @return DataMap
     */
    protected function getDataMap() {
        return $this->dataMap;
    }

} 
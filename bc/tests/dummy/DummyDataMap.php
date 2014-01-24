<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 14:58
 */

namespace bc\tests\dummy;


use bc\model\DataMap;

class DummyDataMap extends DataMap {

    protected $className = 'bc\\tests\\dummy\\DummyModel';

    protected function initSql() {
        $this->findOneSql = "SELECT * FROM test_table WHERE id=:id";
        $this->findAllSql = "SELECT * FROM test_table";
        $this->findByIdsSql = "SELECT * FROM test_table WHERE id IN (:ids)";
        $this->countSql = "SELECT count(id) FROM test_table";
        $this->insertSql = "INSERT INTO test_table (title) VALUES (:title)";
        $this->updateSql = "UPDATE test_table SET title=:title WHERE id=:id";
        $this->deleteSql = "DELETE FROM test_table WHERE id=:id";
    }

    /**
     * @param DummyModel $item
     * @return array
     */
    protected function getInsertBindings($item) {
        return array(
            ':title' => $item->getTitle()
        );
    }

    /**
     * @param DummyModel $item
     * @return array
     */
    protected function getUpdateBindings($item) {
        return array(
            ':title' => $item->getTitle()
        );
    }

    public function fetchAllAsArray() {
        $stmt = $this->prepare($this->findAllSql);
        return $this->fetchByStatement($stmt, true);
    }

}
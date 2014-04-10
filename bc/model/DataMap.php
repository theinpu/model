<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 13:50
 */

namespace bc\model;

use bc\pdo\PDOHelper;

abstract class DataMap {

    protected $findOneSql = '';
    protected $findAllSql = '';
    protected $findByIdsSql = '';
    protected $countSql = '';
    protected $insertSql = '';
    protected $updateSql = '';
    protected $deleteSql = '';

    /**
     * @var \PDOStatement
     */
    private $findOneStatement = null;
    /**
     * @var \PDOStatement
     */
    private $findAllStatement = null;
    /**
     * @var \PDOStatement
     */
    private $findByIdsStatement = null;
    /**
     * @var \PDOStatement
     */
    private $countStatement = null;
    /**
     * @var \PDOStatement
     */
    private $insertStatement = null;
    /**
     * @var \PDOStatement
     */
    private $updateStatement = null;
    /**
     * @var \PDOStatement
     */
    private $deleteStatement = null;

    protected $className = null;

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @throw \Exception
     */
    public function __construct() {
        $this->pdo = PDOHelper::getPDO();
        $this->initSql();
        $this->checkSql();
        $this->prepareStatements();
    }

    protected abstract function initSql();

    /**
     * @param Model $item
     *
     * @return array
     */
    protected abstract function getInsertBindings($item);

    /**
     * @param Model $item
     *
     * @return array
     */
    protected abstract function getUpdateBindings($item);

    /**
     * @param int $id
     *
     * @return Model | null
     */
    public function get($id) {
        $this->findOneStatement->bindValue(':id', $id);
        $item = $this->fetchByStatement($this->findOneStatement);
        if(count($item) == 0) return null;
        return $item[0];
    }

    /**
     * @return Model[]
     */
    public function getAll() {
        return $this->fetchByStatement($this->findAllStatement);
    }

    /**
     * @param array|int $ids
     *
     * @return Model[]
     */
    public function getList($ids) {
        if(!is_array($ids)) {
            $ids = array($ids);
        }
        $placeHolder = implode(',', array_fill(0, count($ids), '?'));
        $this->findByIdsStatement = $this->prepare(str_replace(':ids', $placeHolder, $this->findByIdsSql));
        foreach($ids as $k => $v) {
            $this->findByIdsStatement->bindValue($k + 1, $v);
        }
        return $this->fetchByStatement($this->findByIdsStatement);
    }

    /**
     * @param int $offset
     * @param int $count
     *
     * @return Model[]
     */
    public function getPartial($offset, $count) {
        $sql = $this->findAllSql.' LIMIT ';
        if($count < 1) {
            $count = 1;
        }
        if($offset > 0) {
            $sql .= $offset.','.$count;
        } else {
            $sql .= $count;
        }
        $stmt = $this->prepare($sql);
        return $this->fetchByStatement($stmt);
    }

    /**
     * @return int
     */
    public function count() {
        $this->countStatement->execute();
        $count = $this->countStatement->fetchColumn(0);
        $this->countStatement->closeCursor();
        return $count;
    }

    /**
     * @param Model $item
     *
     * @return void
     */
    public function save(Model $item) {
        if($item instanceof NullObject) return;
        $this->prepareItem($item);
        if(is_null($item->getId())) {
            $this->insert($item);
            $this->onInsert($item);
        } else {
            if($item->isChanged()) {
                $this->update($item);
                $this->onUpdate($item);
            }
        }
        $this->onSave($item);
        $item->onSave();
    }

    /**
     * @param $id
     *
     * @return void
     */
    public function delete($id) {
        $this->deleteStatement->bindValue(':id', $id);
        $this->deleteStatement->execute();
    }

    /**
     * @param Model $item
     */
    protected function insert($item) {
        $bindings = $this->getInsertBindings($item);
        foreach($bindings as $key => $value) {
            if(is_array($value)) {
                $this->insertStatement->bindValue($key, $value[0], $value[1]);
            } else {
                $this->insertStatement->bindValue($key, $value);
            }
        }
        $this->insertStatement->execute();
        $item->setId($this->getPDO()->lastInsertId());
    }

    /**
     * @param Model $item
     */
    protected function update($item) {
        $bindings = $this->getUpdateBindings($item);
        foreach($bindings as $key => $value) {
            if(is_array($value)) {
                $this->updateStatement->bindValue($key, $value[0], $value[1]);
            } else {
                $this->updateStatement->bindValue($key, $value);
            }
        }
        $this->updateStatement->bindValue(':id', $item->getId());
        $this->updateStatement->execute();
    }

    /**
     * @return \PDO
     */
    protected final function getPdo() {
        return $this->pdo;
    }

    /**
     * @param \PDOStatement $stmt
     * @param bool          $asArray
     *
     * @return Model[] | array
     */
    protected function fetchByStatement($stmt, $asArray = false) {
        $stmt->execute();
        if($asArray || is_null($this->className)) {
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $items = $stmt->fetchAll(\PDO::FETCH_CLASS, $this->className);
            foreach($items as &$item) {
                $this->itemCallback($item);
            }
        }

        return $items;
    }

    /**
     * @param Model $item
     *
     * @deprecated will be private
     */
    protected function itemCallback($item) {
        if(is_array($item)) return;
        $this->beforeItemSetup($item);
        $fields = get_object_vars($item);
        foreach($fields as $field => $value) {
            if(isset($item->$field)) {
                if($field == 'id') continue;
                $method = 'set'.ucfirst($field);
                if(method_exists($item, $method)) {
                    call_user_func(array($item, $method), $value);
                }
                unset($item->$field);
            }
        }
        $this->setItemId($item);
        $this->afterItemSetup($item);
        $item->onLoad();
    }

    /**
     * @param Model $item
     */
    private function setItemId($item) {
        if(is_null($item->getId())) {
            $item->setId($item->{'id'});
            unset($item->{'id'});
        }
    }

    protected final function prepare($sql) {
        return $this->getPdo()->prepare($sql);
    }

    /**
     * @param Model $item
     */
    protected function prepareItem($item) {
    }

    /**
     * @param Model $item
     */
    protected function onSave($item) {
    }

    /**
     * @param Model $item
     */
    protected function onInsert($item) {
    }

    /**
     * @param Model $item
     */
    protected function onUpdate($item) {
    }

    private function checkSql() {
        if(empty($this->findOneSql)) {
            throw new \Exception('Need to set findOneSql');
        }
        if(empty($this->findAllSql)) {
            throw new \Exception('Need to set findAllSql');
        }
        if(empty($this->findByIdsSql)) {
            throw new \Exception('Need to set findByIdsSql');
        }
        if(empty($this->countSql)) {
            throw new \Exception('Need to set countSql');
        }
        if(empty($this->insertSql)) {
            throw new \Exception('Need to set insertSql');
        }
        if(empty($this->updateSql)) {
            throw new \Exception('Need to set updateSql');
        }
        if(empty($this->deleteSql)) {
            throw new \Exception('Need to set deleteSql');
        }
    }

    private function prepareStatements() {
        $this->findOneStatement = $this->prepare($this->findOneSql);
        $this->findAllStatement = $this->prepare($this->findAllSql);
        $this->countStatement = $this->prepare($this->countSql);
        $this->insertStatement = $this->prepare($this->insertSql);
        $this->updateStatement = $this->prepare($this->updateSql);
        $this->deleteStatement = $this->prepare($this->deleteSql);
    }

    protected function beforeItemSetup(Model $item) {
    }

    protected function afterItemSetup(Model $item) {
    }
} 
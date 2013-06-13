<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Storage\Database;

class Entity extends \Staq\Core\Data\Stack\Storage\Entity implements \Stack\IEntity
{


    /* ATTRIBUTES
     *************************************************************************/
    public static $setting = [
        'database.idField' => 'id',
        'database.fields' => ['id']
    ];
    protected $settings;
    protected $name;
    protected $table;
    protected $idField;
    protected $fields;


    /* CONSTRUCTOR
     *************************************************************************/
    public function __construct()
    {
        $this->settings = (new \Stack\Setting)->parse($this);
        $this->name = strtolower(\Staq\Util::getStackSubQuery($this, '_'));
        $this->table = $this->settings->get('database.table', $this->name);
        $this->idField = $this->settings['database.idField'];
        $this->fields = $this->settings->getAsArray('database.fields');
    }


    /* FETCHING METHODS
     *************************************************************************/
    public function fetchById($id)
    {
        return $this->fetchByField($this->idField, $id);
    }

    public function fetchByField($field, $value, $limit = NULL)
    {
        return $this->fetchOne([$field => $value], NULL, NULL, $limit);
    }

    public function fetchAll($limit = NULL, $offset = NULL, &$count = FALSE)
    {
        return $this->fetch([], $limit, NULL, $offset, $count);
    }

    public function fetchByIds($ids, $limit = NULL)
    {
        if (empty($ids)) {
            return array();
        }
        return $this->fetch([$this->idField => $ids], $limit);
    }

    public function fetchByRelated($field, $related, $limit = NULL, $offset = NULL, &$count = FALSE)
    {
        return $this->fetch([$field => $related->id], $limit, NULL, $offset = NULL, $count);
    }

    public function deleteByFields($where)
    {
        $parameters = [];
        $sql = 'DELETE FROM ' . $this->table . $this->getClauseByFields($where, $parameters);
        $request = new Request($sql);
        return $request->execute($parameters);
    }

    public function updateRelated($field, $ids, $related)
    {
        if (!empty($ids)) {
            $sql = 'UPDATE ' . $this->table
                . ' SET ' . $field . '=' . $related->id
                . ' WHERE ' . $this->idField . ' IN (' . implode(', ', $ids) . ')';
            $request = new Request($sql);
            $request->execute();
        }
        $sql = 'UPDATE ' . $this->table
            . ' SET ' . $field . '=NULL'
            . ' WHERE ' . $field . '=' . $related->id;
        if (!empty($ids)) {
            $sql .= ' AND ' . $this->idField . ' NOT IN (' . implode(', ', $ids) . ')';
        }
        $request = new Request($sql);
        $request->execute();
    }

    public function fetchByRelatedThroughTable($table, $field, $relatedField, $related)
    {
        $ids = $this->fetchIdsByRelatedThroughTable($table, $field, $relatedField, $related);
        return $this->fetchByIds($ids);
    }

    public function updateRelatedThroughTable($table, $field, $relatedField, $ids, $related)
    {
        $existing = $this->fetchIdsByRelatedThroughTable($table, $field, $relatedField, $related);
        $addIds = array_diff($ids, $existing);
        if (!empty($addIds)) {
            $sql = array();
            foreach ($addIds as $addId) {
                $sql[] = ' (' . $addId . ', ' . $related->id . ')';
            }
            $sql = 'INSERT INTO ' . $table . ' (' . $field . ', ' . $relatedField . ') VALUES' . implode(',', $sql);
            $request = new Request($sql);
            $request->execute();
        }
        $removeIds = array_diff($existing, $ids);
        if (!empty($removeIds)) {
            $sql = 'DELETE FROM ' . $table
                . ' WHERE ' . $relatedField . '=' . $related->id
                . ' AND ' . $field . ' IN (' . implode(', ', $removeIds) . ') ';
            $request = new Request($sql);
            $request->execute();
        }
    }

    public function fetchIdsByRelatedThroughTable($table, $field, $relatedField, $related)
    {
        if (!$related->exists() ) {
            return array();
        }
        $sql = 'SELECT ' . $field . ' FROM ' . $table
            . ' WHERE ' . $relatedField . '=' . $related->id;
        $request = new Request($sql);
        $existing = $request->execute();
        return array_keys(\UArray::keyBy($existing, $field));
    }


    /* MODEL METHODS
     *************************************************************************/
    public function extractId(&$data)
    {
        $id = NULL;
        if (isset($data[$this->idField])) {
            $id = $data[$this->idField];
            unset($data[$this->idField]);
        }
        return $id;
    }

    public function delete($model = NULL)
    {
        $sql = 'DELETE FROM ' . $this->table;
        $parameters = [];
        if (!is_null($model)) {
            if (!$model->exists()) {
                return TRUE;
            }
            $sql .= ' WHERE ' . $this->idField . '=:id';
            $parameters[':id'] = $model->id;
        }
        $request = new Request($sql);
        return $request->executeOne($parameters);
    }

    public function save($model)
    {
        if ($model->exists()) {
            $sql = 'UPDATE ' . $this->table
                . ' SET ' . $this->getSetRequest($model)
                . ' WHERE `' . $this->idField . '` = :' . $this->idField . ' ;';
            $request = new Request($sql);
            $request->executeOne($this->getBindParams($model));
            return $model->id;
        } else {
            $sql = 'INSERT INTO ' . $this->table
                . ' (`' . implode('`, `', $this->fields) . '`) VALUES'
                . ' (:' . implode(', :', $this->fields) . ');';
            $request = new Request($sql);
            $request->executeOne($this->getBindParams($model));
            return $request->getLastInsertId();
        }
    }


    /* PRIVATE FETCH METHODS
     *************************************************************************/
    protected function fetch($fields=[], $limit=NULL, $order=NULL, $offset=NULL, &$count=FALSE)
    {
        if ($limit == 'count') {
            return $this->getCount($fields);
        }
        $data = $this->getDataList($fields, $limit, $order, $offset, $count !== FALSE);
        if ($count !== FALSE) {
            $count = $this->fetchCount();
        }
        return $this->resultAsModelList($data);
    }

    protected function fetchOne($fields=[], $order=NULL, $offset=NULL, $limit=NULL)
    {
        if ($limit == 'count') {
            return $this->getCount($fields);
        }
        $data = $this->getData($fields, $order, $offset);
        return $this->resultAsModel($data);
    }

    protected function fetchCount()
    {
        $parameters = [];
        $sql = 'SELECT FOUND_ROWS();';
        $request = new Request($sql);
        return reset($request->executeOne($parameters));
    }

    protected function getData($where=[], $order=NULL, $offset=NULL)
    {
        $datas = $this->getDataList($where, 1, $order, $offset);
        if (isset($datas[0])) {
            return $datas[0];
        }
        return FALSE;
    }

    protected function getCount($where=[])
    {
        $parameters = [];
        $sql = 'SELECT COUNT(*)'
            . ' FROM ' . $this->getBaseTable()
            . $this->getClauseByFields($where, $parameters);
        $request = new Request($sql);
        return reset($request->executeOne($parameters));
    }

    protected function getDataList($where=[], $limit=NULL, $order=NULL, $offset=NULL, $count=FALSE)
    {
        $parameters = [];
        $sql = $this->getBaseSelect($count)
            . $this->getClauseByFields($where, $parameters, $limit, $order, $offset);
        $request = new Request($sql);
        return $request->execute($parameters);
    }


    /* PRIVATE CORE METHODS
     *************************************************************************/
    protected function getBaseSelect($count = FALSE)
    {
        $select = 'SELECT ';
        if ($count) {
            $select .= 'SQL_CALC_FOUND_ROWS ';
        }
        $select .= $this->getBaseSelector() . ' FROM ' . $this->getBaseTable();
        return $select;
    }

    protected function getBaseSelector()
    {
        $fields = array_map(function ($field) {
            return $this->table . '.' . $field;
        }, $this->fields);
        return implode(', ', $fields);
    }

    protected function getGroupBy()
    {
        return '';
    }

    protected function getDefaultOrder()
    {
        return '';
    }

    protected function getBaseTable()
    {
        return $this->table;
    }

    protected function getDefaultWhere()
    {
        return [];
    }

    protected function getClauseByFields($request, &$parameters, $limit=NULL, $order=NULL, $offset=NULL)
    {
        $where = $this->getDefaultWhere();
        if (is_array($request)) {
            foreach ($request as $fieldName => $fieldValue) {
                if (is_numeric($fieldName)) {
                    if (is_string($fieldValue)) {
                        $where[] = $fieldValue;
                    } else if (
                        is_array($fieldValue) &&
                        isset($fieldValue[0]) &&
                        isset($fieldValue[1]) &&
                        isset($fieldValue[2])
                    ) {
                        $where[] = $this->getClauseCondition($parameters, $fieldValue[0], $fieldValue[1], $fieldValue[2]);
                    }
                } else {
                    if (!\UString::has($fieldName, '.')) {
                        $fieldName = $this->table . '.' . $fieldName;
                    }
                    $where[] = $this->getClauseCondition($parameters, $fieldName, '=', $fieldValue);
                }
            }
        }
        $sql = '';
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= $this->getGroupBy();
        if (is_null($order)) {
            $sql .= $this->getDefaultOrder();
        } else {
            $sql .= ' ORDER BY ' . $order;
        }
        if (!is_null($limit)) {
            $sql .= ' LIMIT ' . $limit;
        }
        if (!is_null($offset)) {
            $sql .= ' OFFSET ' . $offset;
        }
        return $sql . ';';
    }

    protected function getClauseCondition(&$parameters, $fieldName, $operator, $fieldValue)
    {
        $condition = NULL;
        $parameterName = 'key' . count($parameters);
        if (is_array($fieldValue)) {
            $conditionParameters = [];
            foreach ($fieldValue as $key => $value) {
                $conditionParameters[':' . 'key_' . (count($parameters) + $key)] = $value;
            }
            $condition = implode(', ', array_keys($conditionParameters));
            $condition = $fieldName . ' IN ( ' . $condition . ' )';
            $parameters = array_merge($parameters, $conditionParameters);
        } else {
            $condition = $fieldName . ' ' . $operator . ' :' . $parameterName;
            $parameters[':' . $parameterName] = $fieldValue;
        }
        return $condition;
    }

    protected function getSetRequest()
    {
        $request = [];
        foreach ($this->fields as $fieldName) {
            if ($fieldName != $this->idField) {
                $request[] = '`' . $fieldName . '` = :' . $fieldName;
            }
        }
        return implode(', ', $request);
    }

    protected function getBindParams($model)
    {
        $data = $this->getCurrentData($model);
        $bindParams = [];
        foreach ($this->fields as $fieldName) {
            $fieldValue = NULL;
            if (isset($data[$fieldName])) {
                $fieldValue = $data[$fieldName];
            }
            $bindParams[$fieldName] = $fieldValue;
        }
        return $bindParams;
    }

    protected function getCurrentData($model)
    {
        $data = $model->extractSeeds();
        $data[$this->idField] = $model->id;
        return $data;
        // DO: Manage serializes extra fields here ;)
    }
}

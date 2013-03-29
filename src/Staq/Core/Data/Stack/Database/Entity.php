<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Database;

class Entity implements \Stack\IEntity
{


    /*************************************************************************
    ATTRIBUTES
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


    /*************************************************************************
    CONSTRUCTOR
     *************************************************************************/
    public function __construct()
    {
        $this->settings = (new \Stack\Setting)->parse($this);
        $this->name = strtolower(\Staq\Util::getStackSubQuery($this, '_'));
        $this->table = $this->settings->get('database.table', $this->name);
        $this->idField = $this->settings['database.idField'];
        $this->fields = $this->settings->getAsArray('database.fields');
    }


    /*************************************************************************
    FETCHING METHODS
     *************************************************************************/
    public function fetchById($id)
    {
        return $this->fetchByField($this->idField, $id);
    }

    public function fetchByField($field, $value)
    {
        return $this->fetchOne([$field => $value]);
    }

    public function fetchAll($limit = NULL, $order = NULL)
    {
        return $this->fetch([], $limit, $order);
    }

    public function fetchByIds($ids, $limit = NULL)
    {
        return $this->fetch([$this->idField => $ids], $limit);
    }

    public function fetchByRelated($field, $related, $limit = NULL)
    {
        return $this->fetch([$field => $related->id], $limit);
    }

    public function extractId(&$data)
    {
        $id = NULL;
        if (isset($data[$this->idField])) {
            $id = $data[$this->idField];
            unset($data[$this->idField]);
        }
        return $id;
    }

    public function deleteByFields($where)
    {
        $parameters = [];
        $sql = 'DELETE FROM ' . $this->table . $this->getClauseByFields($where, $parameters);
        $request = new Request($sql);
        return $request->execute($parameters);
    }


    /*************************************************************************
    PUBLIC DATABASE REQUEST
     *************************************************************************/
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


    /*************************************************************************
    PRIVATE FETCH METHODS
     *************************************************************************/
    protected function fetch($fields = [], $limit = NULL, $order = NULL)
    {
        $data = $this->getDataList($fields, $limit, $order);
        return $this->resultAsModelList($data);
    }

    protected function fetchOne($fields = [], $order = NULL)
    {
        $data = $this->getData($fields, 1, $order);
        return $this->resultAsModel($data);
    }

    protected function getData($where = [])
    {
        $datas = $this->getDataList($where, 1);
        if (isset($datas[0])) {
            return $datas[0];
        }
        return FALSE;
    }

    protected function getDataList($where = [], $limit = NULL, $order = NULL)
    {
        $parameters = [];
        $sql = $this->getBaseSelect() . $this->getClauseByFields($where, $parameters, $limit, $order);
        $request = new Request($sql);
        return $request->execute($parameters);
    }


    /*************************************************************************
    PRIVATE CORE METHODS
     *************************************************************************/
    protected function getBaseSelect()
    {
        return 'SELECT ' . $this->getBaseSelector() . ' FROM ' . $this->getBaseTable();
    }

    protected function getBaseSelector()
    {
        $fields = array_map(function ($field) {
            return $this->table . '.' . $field;
        }, $this->fields);
        return implode(', ', $fields);
    }

    protected function getBaseTable()
    {
        return $this->table;
    }

    protected function getClauseByFields($request, &$parameters, $limit = NULL, $order = NULL)
    {
        $where = [];
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
        if (!is_null($order)) {
            $sql .= ' ORDER BY ' . $order;
        }
        if (!is_null($limit)) {
            $sql .= ' LIMIT ' . $limit;
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

    protected function getModel()
    {
        $modelClass = 'Stack\\Model\\' . \Staq\Util::getStackSubQuery($this);
        return new $modelClass;
    }

    protected function resultAsModelList($data)
    {
        $list = [];
        $model = $this->getModel();
        foreach ($data as $item) {
            $list[] = $model->byData($item);
        }
        return $list;
    }

    protected function resultAsModel($data)
    {
        return $this->getModel()->byData($data);
    }
}

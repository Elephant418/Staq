<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Database;

class Request
{


    /*************************************************************************
    ATTRIBUTES
     *************************************************************************/
    protected $request;
    protected $PDObject;
    protected $lastInsertId = false;


    /*************************************************************************
    GETTER
     *************************************************************************/
    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    public function getPDObject()
    {
        $this->connect();
        return $this->PDObject;
    }

    public function getRequest()
    {
        return $this->request;
    }


    /*************************************************************************
    SETTER
     *************************************************************************/
    public function setPDObject($PDObject)
    {
        $this->PDObject = $PDObject;
        return $this;
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }


    /*************************************************************************
    CONSTRUCTOR
     *************************************************************************/
    public function __construct($request = '')
    {
        $this->setRequest($request);
    }


    /*************************************************************************
    PUBLIC METHODS
     *************************************************************************/
    public function executeOne($arguments = array())
    {
        $result = $this->execute($arguments);
        if (is_array($result) && count($result) > 0) {
            $result = $result[0];
        }
        return $result;
    }

    public function execute($arguments = array())
    {

        if (empty($this->request)) {
            throw new \Stack\Exception\Database('The SQL request is empty.');
        }

        $result = [];
        try {

            // Prepare the request
            $this->connect();
            $statement = $this->PDObject->prepare($this->request);
            foreach ($arguments as $parameter => $value) {
                $statement->bindValue($parameter, $value);
            }
            $result = $statement->execute();

            // Execute the request
            if (!$result) {
                throw new \Stack\Exception\Database('Error with the SQL request : ' . $this->request, $statement->errorInfo());
            }

            if (\UString::isStartWith($this->request, ['SELECT', 'SHOW', 'DESCRIBE', 'EXPLAIN'])) {
                $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            } else if (\UString::isStartWith($this->request, "INSERT")) {
                $result = TRUE;
                $id = $this->PDObject->lastInsertId();
                if ($id == '0') {
                    // Error Case or a table without autoincrementation
                    $id = FALSE;
                    $result = FALSE;
                }
                $this->lastInsertId = $id;
            }

        } catch (PDOException $exception) {
            throw new \Stack\Exception\Database($exception->getMessage());
        }
        $this->disconnect();
        return $result;
    }

    public function requireDatabase($name = NULL)
    {
        if (is_null($name)) {
            $ini = (new \Stack\Setting)->parse('Database');
            $name = $ini['access.name'];
        }
        $this->connect(FALSE);
        $statement = $this->PDObject->prepare('CREATE DATABASE IF NOT EXISTS `' . $name . '`;');
        $statement->execute();
        $this->disconnect();
        return $this;
    }

    public function loadMysqlFile($file)
    {
        $requests = file_get_contents($file);
        $requests = explode(';', $requests);
        foreach ($requests as $request) {
            $request = trim(preg_replace('@(/\*(.|[\r\n])*?\*/)|(--(.*|[\r\n]))@', '', $request));
            if (!empty($request)) {
                (new \Stack\Database\Request)
                    ->setRequest($request)
                    ->execute();
            }
        }
        return $this;
    }


    /*************************************************************************
    PRIVATE METHODS
     *************************************************************************/
    protected function connect($database = TRUE)
    {
        if (empty($this->PDObject)) {
            $ini = (new \Stack\Setting)->parse('Database');
            $conf = $ini['access.driver'] . ':host=' . $ini['access.host'];
            $this->PDObject = new \PDO($conf, $ini['access.user'], $ini['access.password'], [\PDO::ATTR_PERSISTENT => TRUE]);
            $this->PDObject->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            if ($database) {
                $this->PDObject->query('USE `' . $ini['access.name'] . '`');
            }
        }
    }

    protected function disconnect()
    {
        unset($this->PDObject);
        $this->PDObject = NULL;
    }
}





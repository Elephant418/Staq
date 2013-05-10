<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack;

interface IEntity
{


    /* FETCHING METHODS
     *************************************************************************/
    public function fetchById($id);

    public function fetchByField($field, $value);

    public function fetchAll($limit);

    public function extractId(&$data);

    public function deleteByFields($where);


    /* PUBLIC DATABASE REQUEST
     *************************************************************************/
    public function delete($model);

    public function save($model);
}

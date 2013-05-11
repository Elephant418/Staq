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


    /* MODEL METHODS
     *************************************************************************/
    public function extractId(&$data);

    public function delete($model);

    public function save($model);
}

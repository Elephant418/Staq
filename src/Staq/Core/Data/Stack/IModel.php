<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack;

interface IModel
{


    /* GETTER
     *************************************************************************/
    public function exists();


    /* INITIALIZATION
     *************************************************************************/
    public function byData($data);


    /* PUBLIC DATABASE REQUEST
     *************************************************************************/
    public function delete();

    public function save();

    public function extractSeeds();


    /* SPECIFIC MODEL ACCESSOR METHODS
     *************************************************************************/
    public function getAttribute($index);

    public function get($index);

    public function set($index, $newVal);
}

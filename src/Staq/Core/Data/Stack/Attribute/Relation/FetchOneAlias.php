<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute\Relation;

class FetchOneAlias extends FetchAlias
{


    /*************************************************************************
    DEBUG METHODS
     *************************************************************************/
    public function __toString()
    {
        return $this->get();
    }
}
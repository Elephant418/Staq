<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack\Exception;

class NotAllowedException extends NotAllowedException\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $defaultCode = 403;


    /* CONSTRUCTOR
     *************************************************************************/
    public function byUri($uri = NULL)
    {
        $this->message = 'You are not enough right for this uri "' . $uri . '"';
        return $this;
    }
}

?>
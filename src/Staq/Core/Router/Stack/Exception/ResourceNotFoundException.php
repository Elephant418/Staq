<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack\Exception;

class ResourceNotFoundException extends ResourceNotFound\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $defaultCode = 404;


    /* CONSTRUCTOR
     *************************************************************************/
    public function byUri($uri = NULL)
    {
        $this->message = 'Resource not found for the uri "' . $uri . '"';
        return $this;
    }

    public function byException($exception = NULL)
    {
        $this->message = 'Resource not found for the ' . $exception;
        return $this;
    }
}

?>
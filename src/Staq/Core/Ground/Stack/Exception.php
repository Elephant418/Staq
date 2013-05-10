<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Ground\Stack;

class Exception extends \Exception
{


    /* ATTRIBUTES
     *************************************************************************/
    protected $defaultMessage = NULL;
    protected $defaultCode = 0;


    /* CONSTRUCTOR
     *************************************************************************/
    public function __construct($message = NULL, $code = NULL, \Exception $previous = NULL)
    {
        if (is_null($message)) $message = $this->defaultMessage;
        if (is_null($message)) $message = \Staq\Util::getStackSubQueryText($this);
        if (is_null($code)) $code = $this->defaultCode;
        parent::__construct($message, $code, $previous);
    }

    public function fromPrevious(\Exception $previous)
    {
        $class = get_class($this);
        return new $class(NULL, NULL, $previous);
    }
}

?>
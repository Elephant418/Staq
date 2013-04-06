<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class Text extends Text\__Parent
{


    /*************************************************************************
    PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        return str_replace( PHP_EOL, HTML_EOL, $this->seed );
    }

    public function set($value)
    {
        $value = str_replace( HTML_EOL, PHP_EOL, $value );
        return parent::set($value);
    }
}
<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class Text extends Text\__Parent
{


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        return str_replace( PHP_EOL, HTML_EOL, $this->seed );
    }

    public function getFirstParagraph()
    {
        $paragraphs = explode( PHP_EOL, $this->seed, 1);
        return $paragraphs[0];
    }

    public function getBeginning( $maximum=300, $minimum=200 )
    {
        $beginning = substr($this->seed, 0, $minimum);
        $margin = substr($this->seed, $minimum, $maximum);
        $part = trim( \UString::substrBeforeLast( $margin, [ '. ', '! ', '? '] ) );
        if (empty($part)){
            $part = trim( \UString::substrBeforeLast( $margin, [ ', ', '; ', ': '] ) );
        }
        return $beginning.$part.'...';
    }

    public function set($value)
    {
        $value = str_replace( HTML_EOL, PHP_EOL, $value );
        return parent::set($value);
    }
}
<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class Text extends Text\__Parent
{


    /* PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        $content = $this->cleanText($this->seed);
        return $this->textToHtml($content);
    }

    public function getFirstParagraph()
    {
        $content = $this->cleanText($this->seed);
        $paragraphList = explode(PHP_EOL, $content, 2);
        return $this->textToHtml($paragraphList[0]);
    }

    public function getExceptFirstParagraph()
    {
        $content = $this->cleanText($this->seed);
        $paragraphList = explode(PHP_EOL, $content, 2);
        if (!isset($paragraphList[1])) {
            return '';
        }
        return $this->textToHtml($paragraphList[1]);
    }

    public function getBeginning( $maximum=300, $minimum=200 )
    {
        $seed = strip_tags($this->seed);
        $beginning = substr($seed, 0, $minimum);
        $margin = substr($seed, $minimum, $maximum);
        $part = trim( \UString::substrBeforeLast( $margin, [ '. ', '! ', '? '] ) );
        if (empty($part)){
            $part = trim( \UString::substrBeforeLast( $margin, [ ', ', '; ', ': '] ) );
        }
        if ( strlen($seed) > strlen($beginning.$part) ) {
            return $beginning.$part.'...';
        }
        return $seed;
    }

    public function set($value)
    {
        $value = str_replace( HTML_EOL, PHP_EOL, $value );
        return parent::set($value);
    }


    /* PROTECTED METHODS
     *************************************************************************/
    protected function textToHtml($content)
    {
        $content = '<p>'.preg_replace('#('.PHP_EOL.'\s*)+#', '</p><p>', $content).'</p>';
        return $content;
    }

    protected function cleanText($content)
    {
        $content = preg_replace('#<[/]?p[^>]*>+#', PHP_EOL, $content);
        $content = preg_replace('#^[\r\n\s]+#', '', $content);
        $content = preg_replace('#[\r\n\s]+$#', '', $content);
        $content = preg_replace('# [ ]+#', ' ', $content);
        $content = preg_replace('#'.PHP_EOL.'[\r\n\s]+#', PHP_EOL, $content);
        return $content;
    }
}
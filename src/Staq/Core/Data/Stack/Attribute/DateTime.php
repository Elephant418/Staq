<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class DateTime extends DateTime\__Parent
{

    static $mysql_format = 'Y-m-d H:i:s';
    static $mysql_format_regex = '/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/';


    /*************************************************************************
    PUBLIC USER METHODS
     *************************************************************************/
    public function get()
    {
        return \DateTime::createFromFormat(static::$mysql_format, $this->seed);
    }

    public function set($value)
    {
        if (static::isValid($value)) {
            $this->seed = $value;
        } else if (is_a($value, 'DateTime')) {
            $this->seed = $value->format(static::$mysql_format);
        }
    }

    public function setNow()
    {
        $this->seed = (new \DateTime)->format(static::$mysql_format);
    }

    public static function isValid($value)
    {
        if (is_string($value)) {
            if (preg_match(static::$mysql_format_regex, $value, $matches)) {
                if (checkdate($matches[2], $matches[3], $matches[1])) {
                    return TRUE;
                }
            }
        }
    }


    /*************************************************************************
    DEBUG METHODS
     *************************************************************************/
    public function __toString()
    {
        return '' . $this->getSeed();
    }
}
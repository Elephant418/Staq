<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute;

class Date extends DateTime
{

    static $mysql_format = 'Y-m-d';
    static $mysql_format_regex = '/^(\d{4})-(\d{2})-(\d{2})$/';
}
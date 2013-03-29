<?php

/* This file is part of the Staq project, which is under MIT license */


class Staq
{

    const VERSION = '0.5';


    /*************************************************************************
    STATIC SHORTHAND METHODS
     *************************************************************************/
    public static function App()
    {
        return static::Application();
    }

    public static function Application()
    {
        return \Staq\Server::$application;
    }

    public static function Ctrl($name)
    {
        return static::Application()->getController($name);
    }

}

<?php

/* This file is part of the Ubiq project, which is under MIT license */

namespace Staq\Core\View\Stack\Util;

class UINotification
{


    /*************************************************************************
    CONSTANTS
     *************************************************************************/
    const INFO = 'info';
    const SUCCESS = 'success';
    const ERROR = 'error';


    /*************************************************************************
    PUSH METHODS
     *************************************************************************/
    public static function info($message)
    {
        static::push($message, static::INFO);
    }

    public static function success($message)
    {
        static::push($message, static::SUCCESS);
    }

    public static function error($message)
    {
        static::push($message, static::ERROR);
    }

    public static function push($message, $type = 'info')
    {
        $info = array('message' => $message, 'type' => $type);
        if (!isset($_SESSION['Staq']['UINotification'])) {
            $_SESSION['Staq']['UINotification'] = array();
        }
        $_SESSION['Staq']['UINotification'][] = $info;
    }


    /*************************************************************************
    PULL METHODS
     *************************************************************************/
    public static function pull()
    {
        $messages = array();
        if (isset($_SESSION['Staq']['UINotification'])) {
            $messages = $_SESSION['Staq']['UINotification'];
            $_SESSION['Staq']['UINotification'] = array();
        }
        return $messages;
    }
}
<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack\Controller;

class ErrorController extends Error\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    public static $setting = [
        'route.view.uri' => '/error/:code',
        'route.view.exceptions' => ['403', '404', '500', 'PDOException']
    ];


    /* ACTION METHODS
     *************************************************************************/
    public function actionView($code)
    {
        if (!headers_sent()) {
            if ($code == '403') {
                header('HTTP/1.1 403 Forbidden');
            } else if ($code == '404') {
                header('HTTP/1.1 404 Not Found');
            } else {
                header('HTTP/1.1 500 Internal Server Error');
            }
        }
        return 'Error ' . $code . '!';
    }

}

?>
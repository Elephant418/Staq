<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Test\Staq\Project\Router\Stack\Controller;

class ErrorController extends ErrorController\__Parent
{


    /* ACTION METHODS
     *************************************************************************/
    public function actionView($code)
    {
        return 'error ' . $code;
    }

}

?>
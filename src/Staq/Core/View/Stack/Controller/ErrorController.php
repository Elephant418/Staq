<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\View\Stack\Controller;

class ErrorController extends Error\__Parent
{


    /* ACTION METHODS
     *************************************************************************/
    public function actionView($code)
    {
        parent::actionView($code);
        $message = '';
        $exception = \Staq::App()->getLastException();
        if ($exception && \Staq::App()->settings->getAsBoolean('error.display_errors')) {
            $message = $exception->getMessage() . HTML_EOL . $exception->getTraceAsString();
        }
        $page = new \Stack\View\Error;
        $page['code'] = $code;
        $page['message'] = $message;
        return $page;
    }

}

?>
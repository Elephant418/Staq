<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\View\Stack;

class Router extends Router\__Parent
{


    /*************************************************************************
    PRIVATE METHODS
     *************************************************************************/
    protected function render($view)
    {
        $view = parent::render($view);
        if (!\Staq\Util::isStack($view, 'Stack\\View')) {
            $page = new \Stack\View;
            $page['content'] = $view;
            $view = $page;
        }
        return $view->render();
    }
}
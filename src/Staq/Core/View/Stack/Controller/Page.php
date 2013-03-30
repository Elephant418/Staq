<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack\Controller;

class Page extends Page\__Parent
{


    /*************************************************************************
    ATTRIBUTES
     *************************************************************************/
    public static $setting = [
        'route.view.uri' => '/(:page)'
    ];


    /*************************************************************************
    ACTION METHODS
     *************************************************************************/
    public function actionView($page='index')
    {
        $path = \Staq::App()->getFilePath('page/' . $page . '.html');
        if ( $path !== FALSE ) {
            $page = new \Stack\View\Page;
            $page['content'] = file_get_contents($path);
            return $page;
        }
    }
}

?>
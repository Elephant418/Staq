<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\View\Stack\Controller;

class Page extends Page\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    public static $setting = [
        'route.view.uri' => '/(:name)'
    ];


    /* ACTION METHODS
     *************************************************************************/
    public function actionView($name='index')
    {
        $path = \Staq::App()->getFilePath('page/' . $name . '.html');
        if (!empty($path)) {
            $page = new \Stack\View\Page;
            $page['content'] = file_get_contents($path);
            $page['page'] = ucfirst( $name );
            return $page;
        }
    }
}

?>
<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Controller\Model;

class Page extends Page\__Parent
{


    /* ATTRIBUTES
     *************************************************************************/
    public static $setting = [
        'route.view.uri' => '/(:name)'
    ];


    /* ACTION METHODS
     *************************************************************************/
    public function actionView($id='index')
    {
        return parent::actionView($id);
    }
}

?>
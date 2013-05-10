<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack;

class View extends View\__Parent
{


    /* PRIVATE METHODS
     *************************************************************************/
    protected function addVariables()
    {
        parent::addVariables();
        $this['currentUser'] = \Staq::Ctrl('Auth')->currentUser();
    }
}

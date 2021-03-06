<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack;

class Router extends Router\__Parent
{


    /* PRIVATE METHODS
     *************************************************************************/
    protected function callController($controller, $action, $route)
    {
        $controllers = $this->setting->getAsArray('auth.controller');
        $exclude = ($this->setting['auth.mode'] == 'exclude');
        $level = $this->setting->get('auth.level', 0);
        $inner = in_array($controller, $controllers);
        if ($exclude xor $inner) {
            if (!\Staq::Ctrl('Auth')->isLogged()) {
                throw new \Stack\Exception\MustBeLogged();
            }
            $user = \Staq::Ctrl('Auth')->currentUser();
            if ($user->hasAttribute('right')){
                if ($user->getAttribute('right')->getSeed() < $level) {
                    throw new \Stack\Exception\NotAllowed();
                }
            }
        }
        $result = parent::callController($controller, $action, $route);
        return $result;
    }
}

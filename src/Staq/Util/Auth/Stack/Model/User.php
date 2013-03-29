<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack\Model;

class User extends User\__Parent
{

    public function byLogin($login)
    {
        return $this->byField('login', $login);
    }
}

<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack\Entity;

class User extends User\__Parent
{

    public function fetchByLogin($login)
    {
        return $this->fetchByField('login', $login);
    }
}

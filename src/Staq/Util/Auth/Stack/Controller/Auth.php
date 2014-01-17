<?php

/* This file is part of the Staq project, which is under MIT license */

namespace Staq\Util\Auth\Stack\Controller;

use \Stack\Util\Form;
use \Stack\Util\UINotification as Notif;

class Auth extends Auth\__Parent
{


    /* CONSTANTS
     *************************************************************************/
    const MSG_INSCRIPTION_VALID = 'You are now connected as %s.';
    const MSG_INSCRIPTION_KO = 'This username is not free.';
    const MSG_LOGIN_VALID = 'You are now connected as %s.';
    const MSG_LOGIN_KO = 'Wrong credentials.';
    const MSG_LOGOUT_VALID = 'You are now deconnected.';


    /* ATTRIBUTES
     *************************************************************************/
    static public $currentUser;
    public static $setting = [
        'route.inscription.uri' => '/inscription',
        'route.login.uri' => '/login',
        'route.login.exceptions' => 'MustBeLogged',
        'route.logout.uri' => '/logout'
    ];


    /* FORMS METHODS
     *************************************************************************/
    public function getInscriptionForm()
    {
        $codes = $this->getCodes();
        return (new Form)
            ->addInput('login', 'string|required|min_length:4|max_length:19', 'inscription.login')
                ->addInputFilter('login', 'validate_regexp:/^[a-zA-Z0-9-]*$/', 'This field must contains only letters, numbers and dash')
            ->addInput('password', 'required', 'inscription.password')
            ->addInput('code', 'required', 'inscription.code')
                ->addInputFilter('code', 'validate_code', function ($field) use ($codes) {
                    return in_array($field, $codes);
                }, 'This is not a valid beta code');
    }

    public function getLoginForm()
    {
        return (new Form)
            ->addInput('login', 'string|required', 'login.login')
            ->addInput('password', 'required', 'login.password');
    }


    /* ACTION METHODS
     *************************************************************************/
    public function actionInscription()
    {
        $form = $this->getInscriptionForm();
        if ($form->isValid()) {
            $user = (new \Stack\Model\User)
                ->set('login', $form->login)
                ->set('password', $form->password)
                ->set('code_beta', $form->code);
            try {
                $saved = FALSE;
                $saved = $user->save();
            } catch (\PDOException $e) {
            }
            if ($saved) {
                $this->login($user);
                Notif::success(sprintf(static::MSG_INSCRIPTION_VALID, $form->login));
                \Staq\Util::httpRedirectUri($this->getRedirectUri());
            } else {
                Notif::error(static::MSG_INSCRIPTION_KO);
            }
        }
        $view = $this->createView('inscription');
        $view['form'] = $form;
        $view['redirect'] = $this->getRedirectUri();
        return $view;
    }

    public function actionLogin()
    {
        $form = $this->getLoginForm();
        if ($form->isValid()) {
            if ($this->login($form->login, $form->password)) {
                Notif::success(sprintf(static::MSG_LOGIN_VALID, $form->login));
                \Staq\Util::httpRedirectUri($this->getRedirectUri());
            } else {
                Notif::error(static::MSG_LOGIN_KO);
            }
        }
        $view = $this->createView('login');
        $view['form'] = $form;
        $view['redirect'] = $this->getRedirectUri();
        return $view;
    }

    public function actionLogout()
    {
        $redirect = '/';
        if (isset($_GET['redirect'])) {
            $redirect = $_GET['redirect'];
        }
        $this->logout();
        Notif::info(static::MSG_LOGOUT_VALID);
        \Staq\Util::httpRedirectUri($redirect);
    }


    /* PROTECTED METHODS
     *************************************************************************/
    protected function getRedirectUri()
    {
        if (isset($_POST['redirect'])) {
            return $_POST['redirect'];
        }
        if (isset($_GET['redirect'])) {
            return $_GET['redirect'];
        }
        return \Staq::App()->getCurrentUri();
    }

    protected function getCodes()
    {
        return (new \Stack\Setting)
            ->parse($this)
            ->getAsArray('code');
    }


    /* UTIL METHODS
     *************************************************************************/
    public function currentUser()
    {

        // Already initialized
        if (!is_null(static::$currentUser)) {
            return static::$currentUser;
        }

        // Find the current user
        if (!isset($_SESSION['Staq']['loggedUser'])) {
            $user = FALSE;
        } else {
            $user = (new \Stack\Entity\User)->fetchById($_SESSION['Staq']['loggedUser']);
            if (!$user->exists()) {
                $user = FALSE;
            }
        }
        static::$currentUser = $user;
        return static::$currentUser;
    }

    public function login($user, $password = NULL)
    {
        if (!is_object($user)) {
            $user = (new \Stack\Entity\User)->fetchByLogin($user);
        }
        if (!is_object($user) || !$user->exists()) {
            return FALSE;
        }
        if (!is_null($password)) {
            if (!$user->getAttribute('password')->compare($password)) {
                return FALSE;
            }
        }
        $_SESSION['Staq']['loggedUser'] = $user->id;
        static::$currentUser = $user;
        return TRUE;
    }

    public function isLogged()
    {
        return ($this->currentUser() !== FALSE);
    }

    public function logout()
    {
        if (isset($_SESSION['Staq']['loggedUser'])) {
            unset($_SESSION['Staq']['loggedUser']);
        }
        static::$currentUser = NULL;
    }
}

?>
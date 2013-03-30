<?php

/* This file is part of the Staq project, which is under MIT license */

namespace Staq\Util\Auth\Stack\Controller;

use \Stack\Util\UINotification as Notif;

class Auth extends Auth\__Parent
{


    /*************************************************************************
    CONSTANTS
     *************************************************************************/
    const MSG_INSCRIPTION_VALID = 'You are now connected as %s.';
    const MSG_INSCRIPTION_KO = 'This username is not free.';
    const MSG_LOGIN_VALID = 'You are now connected as %s.';
    const MSG_LOGIN_KO = 'Wrong credentials.';
    const MSG_LOGOUT_VALID = 'You are now deconnected.';


    /*************************************************************************
    ATTRIBUTES
     *************************************************************************/
    static public $currentUser;
    public static $setting = [
        'route.inscription.uri' => '/inscription',
        'route.login.uri' => '/login',
        'route.login.exceptions' => 'MustBeLogged',
        'route.logout.uri' => '/logout'
    ];


    /*************************************************************************
    FORMS METHODS
     *************************************************************************/
    public function getInscriptionForm()
    {
        $codes = $this->getCodes();
        \Stack\Util\FormFilter::addCustomFilter('existing_code', function ($options) use ($codes) {
            return function ($field) use ($codes) {
                return in_array($field, $codes);
            };
        });
        return (new \Stack\Util\FormHelper)
            ->addField('login', 'inscription.login')
            ->addFilter('login', FILTER_SANITIZE_STRING)
            ->addFilter('login', 'required', 'This field is required')
            ->addFilter('login', FILTER_VALIDATE_REGEXP, 'This field must contains only letters, numbers and dash', ['regexp' => '/^[a-zA-Z0-9-]*$/'])
            ->addFilter('login', 'min_length', 'This field must contains at least 4 characters', ['length' => '4'])
            ->addFilter('login', 'max_length', 'This field must contains less than 20 characters', ['length' => '19'])
            ->addField('password', 'inscription.password')
            ->addFilter('password', 'required', 'This field is required')
            ->addField('code', 'inscription.code')
            ->addFilter('code', 'required', 'This field is required')
            ->addFilter('code', 'existing_code', 'This is not a valid beta code');
    }

    public function getLoginForm()
    {
        return (new \Stack\Util\FormHelper)
            ->addField('login', 'login.login')
            ->addFilter('login', FILTER_SANITIZE_STRING)
            ->addFilter('login', 'required', 'This field is required')
            ->addField('password', 'login.password')
            ->addFilter('password', 'required', 'This field is required');
    }


    /*************************************************************************
    ACTION METHODS
     *************************************************************************/
    public function actionInscription()
    {
        $form = $this->getInscriptionForm();
        if ($form->isValid()) {
            $user = (new \Stack\Model\User)
                ->set('login', $form->get('login'))
                ->set('password', $form->get('password'))
                ->set('code_beta', $form->get('code'));
            try {
                $saved = FALSE;
                $saved = $user->save();
            } catch (\PDOException $e) {
            }
            if ($saved) {
                $this->login($user);
                Notif::success(sprintf(static::MSG_INSCRIPTION_VALID, $form->get('login')));
                \Staq\Util::httpRedirectUri($this->getRedirectUri());
            } else {
                Notif::error(static::MSG_INSCRIPTION_KO);
            }
        }
        $page = new \Stack\View\Auth\Inscription;
        $page['form'] = $form;
        $page['redirect'] = $this->getRedirectUri();
        return $page;
    }

    public function actionLogin()
    {
        $form = $this->getLoginForm();
        if ($form->isValid()) {
            if ($this->login($form->getFieldValue('login'), $form->getFieldValue('password'))) {
                Notif::success(sprintf(static::MSG_LOGIN_VALID, $form->getFieldValue('login')));
                \Staq\Util::httpRedirectUri($this->getRedirectUri());
            } else {
                Notif::error(static::MSG_LOGIN_KO);
            }
        }
        $page = new \Stack\View\Auth\Login;
        $page['form'] = $form;
        $page['redirect'] = $this->getRedirectUri();
        return $page;
    }

    public function actionLogout()
    {
        $this->logout();
        Notif::info(static::MSG_LOGOUT_VALID);
        \Staq\Util::httpRedirectUri('/');
    }


    /*************************************************************************
    PROTECTED METHODS
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


    /*************************************************************************
    UTIL METHODS
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
        if (!$user->exists()) {
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
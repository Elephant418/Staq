<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack\Controller;

class Auth extends Auth\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	static public $current_user;
	public static $setting = [
		'route.inscription.uri'  => '/inscription',
		'route.login.uri'        => '/login',
		'route.login.exceptions' => 'NotAllowed',
		'route.logout.uri'       => '/logout'
	];



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function action_inscription( ) {
		return 'Inscription';
	}

	public function action_login( ) {
		return 'Login';
	}

	public function action_logout( ) {
		return 'Logout';
	}



	/*************************************************************************
	  UTIL METHODS           
	 *************************************************************************/
	public function current_user( ) {

		// Already initialized
		if ( ! is_null( static::$current_user ) ) {
		    return static::$current_user;
		}

		// Find the current user
		if ( ! isset( $_SESSION[ 'Staq' ][ 'logged_user' ] ) ) {
		    $user = FALSE;
		} else {
			$user = ( new \Stack\Model\User )->by_id( $_SESSION[ 'Staq' ][ 'logged_user' ] );
			if ( ! $user->exists( ) ) {
				$user = FALSE;
			}
		}
		static::$current_user = $user;
		return static::$current_user;
	}

	public function login( $user ) {
		$_SESSION[ 'Staq' ][ 'logged_user' ] = $user->id;
		static::$current_user = $user;
	}

	public function is_logged( ) {
		return ( $this->current_user( ) !== FALSE );
	}

	public function logout( ) {
		unset( $_SESSION[ 'Supersoniq' ][ 'user' ] );
		static::$current_user = NULL;
	}

}

?>
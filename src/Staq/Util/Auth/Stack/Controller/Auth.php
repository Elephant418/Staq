<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack\Controller;

class Auth extends Auth\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	const CRYPT_SEED = 'dacz:;,aafapojn';
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
		$code = ''; 
		$login = ''; 
		$bad_credentials = FALSE;
		$bad_code = FALSE;
		if ( isset( $_POST[ 'inscription' ][ 'login' ] ) ) {
			$login = $_POST[ 'inscription' ][ 'login' ];
			if ( isset( $_POST[ 'inscription' ][ 'code' ] ) ) {
				$code = $_POST[ 'inscription' ][ 'code' ];
				$match = ( new \Stack\Setting )
					->parse( $this )
					->getAsArray( 'code' );
				if ( in_array( $code, $match ) ) {
					if ( isset( $_POST[ 'inscription' ][ 'password' ] ) ) {
						$password = $_POST[ 'inscription' ][ 'password' ];
						$password = $this->encrypt_password( $password );
						$user = ( new \Stack\Model\User )
							->set( 'login', $login )
							->set( 'password', $password )
							->set( 'code', $code );
						$saved = FALSE;
						try {
							$saved = $user->save( );
						} catch ( \PDOException $e ) { }
						if ( $saved ) {
							$this->login( $user );
							\Staq\Util::httpRedirect( $this->get_redirect_uri( ) );
						} else {
							$bad_credentials = TRUE;
						}
					}
				} else {
					$bad_code = TRUE;
				}
			}
		}
		$page = new \Stack\View\Auth\Inscription;
		$page[ 'login' ]    = $login;
		$page[ 'code' ]     = $code;
		$page[ 'redirect' ] = $this->get_redirect_uri( );
		$page[ 'bad_code' ] = $bad_code;
		$page[ 'bad_credentials' ] = $bad_credentials;
		return $page;
	}

	public function action_login( ) {
		$login = ''; 
		$bad_credentials = FALSE;
		if ( isset( $_POST[ 'login' ][ 'login' ] ) ) {
			$login = $_POST[ 'login' ][ 'login' ];
			if ( isset( $_POST[ 'login' ][ 'password' ] ) ) {
				$password = $_POST[ 'login' ][ 'password' ];
				if ( $this->login( $login, $password ) ) {
					\Staq\Util::httpRedirect( $this->get_redirect_uri( ) );
				} else {
					$bad_credentials = TRUE;
				}
			}
		}
		$page = new \Stack\View\Auth\Login;
		$page[ 'login' ] = $login;
		$page[ 'redirect' ] = $this->get_redirect_uri( );
		$page[ 'bad_credentials' ] = $bad_credentials;
		return $page;
	}

	public function action_logout( ) {
		$this->logout( );
		\Staq\Util::httpRedirect( '/' . \Staq::App()->get_base_uri( ) );
	}



	/*************************************************************************
	  PROTECTED METHODS           
	 *************************************************************************/
	protected function get_redirect_uri( ) {
		if ( isset( $_POST[ 'redirect' ] ) ) {
			return $_POST[ 'redirect' ];
		}
		if ( isset( $_GET[ 'redirect' ] ) ) {
			return $_GET[ 'redirect' ];
		}
		return \Staq::App()->get_current_uri( );
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

	public function login( $user, $password = NULL ) {
		if ( ! is_object( $user ) ) {
			$user = ( new \Stack\Model\User )->by_login( $user );
		}
		if ( ! $user->exists( ) ) {
			return FALSE;
		}
		if ( ! is_null( $password ) ) {
			$password = $this->encrypt_password( $password );
			if ( $user->password !== $password ) {
				return FALSE;
			}
		}
		$_SESSION[ 'Staq' ][ 'logged_user' ] = $user->id;
		static::$current_user = $user;
		return TRUE;
	}

	public function is_logged( ) {
		return ( $this->current_user( ) !== FALSE );
	}

	public function logout( ) {
		if ( isset( $_SESSION[ 'Staq' ][ 'logged_user' ] ) ) {
			unset( $_SESSION[ 'Staq' ][ 'logged_user' ] );
		}
		static::$current_user = NULL;
	}

	public function encrypt_password( $password ) {
		return sha1( static::CRYPT_SEED . $password );
	}
}

?>
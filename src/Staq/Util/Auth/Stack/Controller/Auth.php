<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack\Controller;

class Auth extends Auth\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	const CRYPT_SEED = 'dacz:;,aafapojn';
	static public $currentUser;
	public static $setting = [
		'route.inscription.uri'  => '/inscription',
		'route.login.uri'        => '/login',
		'route.login.exceptions' => 'NotAllowed',
		'route.logout.uri'       => '/logout'
	];



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function actionInscription( ) {
		$code = ''; 
		$login = ''; 
		$badCredentials = FALSE;
		$badCode = FALSE;
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
						$password = $this->encryptPassword( $password );
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
							\Staq\Util::httpRedirect( $this->getRedirectUri( ) );
						} else {
							$badCredentials = TRUE;
						}
					}
				} else {
					$badCode = TRUE;
				}
			}
		}
		$page = new \Stack\View\Auth\Inscription;
		$page[ 'login' ]    = $login;
		$page[ 'code' ]     = $code;
		$page[ 'redirect' ] = $this->getRedirectUri( );
		$page[ 'badCode' ] = $badCode;
		$page[ 'badCredentials' ] = $badCredentials;
		return $page;
	}

	public function actionLogin( ) {
		$login = ''; 
		$badCredentials = FALSE;
		if ( isset( $_POST[ 'login' ][ 'login' ] ) ) {
			$login = $_POST[ 'login' ][ 'login' ];
			if ( isset( $_POST[ 'login' ][ 'password' ] ) ) {
				$password = $_POST[ 'login' ][ 'password' ];
				if ( $this->login( $login, $password ) ) {
					\Staq\Util::httpRedirect( $this->getRedirectUri( ) );
				} else {
					$badCredentials = TRUE;
				}
			}
		}
		$page = new \Stack\View\Auth\Login;
		$page[ 'login' ] = $login;
		$page[ 'redirect' ] = $this->getRedirectUri( );
		$page[ 'badCredentials' ] = $badCredentials;
		return $page;
	}

	public function actionLogout( ) {
		$this->logout( );
		\Staq\Util::httpRedirect( '/' . \Staq::App()->getBaseUri( ) );
	}



	/*************************************************************************
	  PROTECTED METHODS           
	 *************************************************************************/
	protected function getRedirectUri( ) {
		if ( isset( $_POST[ 'redirect' ] ) ) {
			return $_POST[ 'redirect' ];
		}
		if ( isset( $_GET[ 'redirect' ] ) ) {
			return $_GET[ 'redirect' ];
		}
		return \Staq::App()->getCurrentUri( );
	}



	/*************************************************************************
	  UTIL METHODS           
	 *************************************************************************/
	public function currentUser( ) {

		// Already initialized
		if ( ! is_null( static::$currentUser ) ) {
		    return static::$currentUser;
		}

		// Find the current user
		if ( ! isset( $_SESSION[ 'Staq' ][ 'loggedUser' ] ) ) {
		    $user = FALSE;
		} else {
			$user = ( new \Stack\Model\User )->byId( $_SESSION[ 'Staq' ][ 'loggedUser' ] );
			if ( ! $user->exists( ) ) {
				$user = FALSE;
			}
		}
		static::$currentUser = $user;
		return static::$currentUser;
	}

	public function login( $user, $password = NULL ) {
		if ( ! is_object( $user ) ) {
			$user = ( new \Stack\Model\User )->byLogin( $user );
		}
		if ( ! $user->exists( ) ) {
			return FALSE;
		}
		if ( ! is_null( $password ) ) {
			$password = $this->encryptPassword( $password );
			if ( $user->password !== $password ) {
				return FALSE;
			}
		}
		$_SESSION[ 'Staq' ][ 'loggedUser' ] = $user->id;
		static::$currentUser = $user;
		return TRUE;
	}

	public function isLogged( ) {
		return ( $this->currentUser( ) !== FALSE );
	}

	public function logout( ) {
		if ( isset( $_SESSION[ 'Staq' ][ 'loggedUser' ] ) ) {
			unset( $_SESSION[ 'Staq' ][ 'loggedUser' ] );
		}
		static::$currentUser = NULL;
	}

	public function encryptPassword( $password ) {
		return sha1( static::CRYPT_SEED . $password );
	}
}

?>
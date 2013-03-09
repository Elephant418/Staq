<?php

/* This file is part of the Staq project, which is under MIT license */

namespace Staq\Util\Auth\Stack\Controller;

use \Stack\Util\UINotification as Notif;

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
		$codes = $this->getCodes( );
		$form = ( new \Stack\Util\FormHelper )
			->addField( 'inscription.login', 'login' )
			->addConstraint( 'login', 'required' )
			->addField( 'inscription.password', 'password' )
			->addConstraint( 'password', 'required' )
			->addField( 'inscription.code', 'code' )
			->addConstraint( 'code', 'required' )
			->addConstraint( 'code', function( $field ) use( $codes ){
				return in_array( $field, $codes );
			}, 'Bad Code' );

		$values = $form->getValues( );
		if ( $form->isValid( ) ) {
			$password = $this->encryptPassword( $values[ 'password' ] );
			$user = ( new \Stack\Model\User )
				->set( 'login', $values[ 'login' ] )
				->set( 'password', $password )
				->set( 'code', $values[ 'code' ] );
			try {
				$saved = $user->save( );
			} catch ( \PDOException $e ) {
			}
			if ( $saved ) {
				$this->login( $user );
				Notif::success( 'You are now connected as ' . $values[ 'login' ] );
				\Staq\Util::httpRedirect( $this->getRedirectUri( ) );
			} else {
				Notif::error( 'This username is not free' );
			}
		}
		$page = new \Stack\View\Auth\Inscription;
		$page[ 'form' ] = $values;
		$page[ 'formErrors' ] = $form->getErrors( );
		$page[ 'redirect' ] = $this->getRedirectUri( );
		return $page;
	}

	public function actionLogin( ) {
		$form = ( new \Stack\Util\FormHelper )
			->addField( 'inscription.login', 'login' )
			->addConstraint( 'login', 'required' )
			->addField( 'inscription.password', 'password' )
			->addConstraint( 'password', 'required' );

		$values = $form->getValues( );
		if ( $form->isValid( ) ) {
			if ( $this->login( $login, $password ) ) {
				Notif::success( 'You are now connected as ' . $values[ 'login' ] );
				\Staq\Util::httpRedirect( $this->getRedirectUri( ) );
			} else {
				Notif::error( 'Wrong credentials' );
			}
		}

		$page = new \Stack\View\Auth\Login;
		$page[ 'form' ] = $values;
		$page[ 'formErrors' ] = $form->getErrors( );
		$page[ 'redirect' ] = $this->getRedirectUri( );
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

	protected function getCodes( ) {
		return ( new \Stack\Setting )
			->parse( $this )
			->getAsArray( 'code' );
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
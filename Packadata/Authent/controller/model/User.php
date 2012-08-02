<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Authent\Controller\Model;


class User extends  User\__Parent {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $current_user;



	/*************************************************************************
	  LOGIN ACTION				   
	 *************************************************************************/
	public function login( $login, $password ) {
		$user = ( new \Model )->by_type( 'User' )->by_login( $login );
		if ( $user->exists( ) ) {
			if ( $user->check_password( $password ) ) {
				$_SESSION[ 'Supersoniq' ][ 'user' ] = $user->id;
				\Notification::push( 'You are logged in ! ', \Notification::SUCCESS );
				return TRUE;
			} else {
				// TODO: Compter le nombre d'erreur de login
				\Notification::push( 'Wrong password ! ', \Notification::ERROR );
			}
		} else {
			\Notification::push( 'Unknown login ! ', \Notification::ERROR );
		}
		return FALSE;
	}



	/*************************************************************************
	  LOGOUT ACTION				   
	 *************************************************************************/
	public function logout( ) {
		unset( $_SESSION[ 'Supersoniq' ][ 'user' ] );
	}


	/*************************************************************************
	  EDITION ACTION				   
	 *************************************************************************/
	public function current_user( ) {

		// Already initialized
		if ( ! is_null( $this->current_user ) ) {
		    return $this->current_user;
		}

		// Find the current user
		if ( ! isset( $_SESSION['Supersoniq'][ 'user' ] ) ) {
		    $user = FALSE;
		} else {
			$user = new \Model\User( );
		    if ( ! $user->init_by_id( $_SESSION['Supersoniq'][ 'user' ] ) ) {
				$user = FALSE;
		    }
		}
		$this->current_user = $user;
		return $this->current_user;
	}

	public function must_logged( ) {
		if ( $current_user = $this->current_user( ) ) {
		    return $current_user;
		} else {
			\Notification::push( 'You must be connected to see this page.', \Notification::ERROR );
			\Supersoniq\Application::redirect_to_action( 'User', 'login', $error );
		}
	}

	public function is_logged( ) {
		return ( $this->current_user( ) !== FALSE );
	}
}

<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Provi\Controller;

class User extends  \Controller\__Base {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $handled_routes = array( 
		'login'    => '/user/login',
		'logout'   => '/user/logout',
		'register' => '/user/register',
		'activate' => '/user/activate/:id/:code',
		'edit'     => '/user/edit'
	);


	/*************************************************************************
	  LOGIN ACTION                   
	 *************************************************************************/
	public function login( ) {
		$error = '';
		
		// User already connected
		if ( isset( $_SESSION[ 'Packadata' ][ 'user' ] ) ) {
			header( 'Location: /' );
			die;
		}

		// Connection validation
		if ( isset( $_POST[ 'login' ] ) ) {
			$user = new \Model\User( );
			if ( $user->init_by_login( $_POST['login'] ) ) {
				if ( $user->check_password( $_POST['password'] ) ) {
					if ( $user->activate ) {
						$_SESSION[ 'Packadata' ][ 'user' ] = $user->id;
						\Notification::push( 'You are logged in ! ', \Notification::SUCCESS );
						\Supersoniq\Application::redirect( '/' );
					} else {
						\Notification::push( 'User not activated ! ', \Notification::ERROR );
					}
				} else {
					// TODO: Compter le nombre d'erreur de login
					\Notification::push( 'Wrong password ! ', \Notification::ERROR );
				}
			} else {
				\Notification::push( 'Unknown login ! ', \Notification::ERROR );
			}
		}
		
		// Connection Form 
		if ( ! isset( $_SESSION[ 'Packadata' ][ 'user' ] ) ) {
			$this->view->title = 'Login page';
			$this->view->content = $this->render( 'user_login_form.html' ); 
		}

		// Render
		return $this->render( \View\__Base::LAYOUT_TEMPLATE );
	}


	/*************************************************************************
	  LOGOUT ACTION                   
	 *************************************************************************/
	public function logout( ) {
		unset( $_SESSION[ 'Packadata' ][ 'user' ] );
		header( 'Location: /' );
		die;
	}


	/*************************************************************************
	  REGISTER ACTION                   
	 *************************************************************************/
	public function register( ) {
		if ( $_POST ) {
			$user = new \Model\User( );
			$user->login = $_POST['login'];
			$user->email = $_POST['email'];
			$user->password = $user->encrypt_password( $_POST['password'] );
			$user->name = $_POST['name'];
			$user->lastname = $_POST['lastname'];
			if ( $user->save( ) ) {
				$user->activation_code = $user->id . \String::random( 20 );
				$user->save( );
				$activation_url = $_SERVER[ 'SERVER_NAME' ] . '/user/activate/' . $user->id . '/' . $user->activation_code;
				$mailer = new \Mail( TRUE );
				$send   = $mailer->setTo( $user->email, $user->name( ) )
						->setSubject( 'Activate your account' )
						->setFrom( 'noreply@supersoniq.org', 'Supersoniq' )
						->addGenericHeader( 'Content-Type', 'text/html; charset="utf-8"' )
						->setMessage( 'Here is the activation url : <a href="' . $activation_url . '">' . $activation_url . '</a>' )
						->setWrap(100)
						->send( );
				if ( $send ) {
					\Notification::push( 'You are registered. A mail was sent to you to activate your account ! ', \Notification::SUCCESS );
					\Supersoniq\Application::redirect_to_action( 'user', 'login' );
				} else {
					\Notification::push( 'We can not send your activation email ! ', \Notification::ERROR );
				}
			}
			\Notification::push( 'There is a problem ! ', \Notification::ERROR );
		}
		$this->view->title = 'Register page';
		$this->view->content = $this->render( 'user_register_form.html' ); 
		return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
	}


	/*************************************************************************
	  ACTIVATION ACTION                   
	 *************************************************************************/
	public function activate( $id, $activation_code ) {
		$user = new \Model\User( );
		if ( $user->init_by_id( $id ) ) {
			if ( $user->activation_code == $activation_code ) {
				$user->activation_code = '';
				$user->activate = TRUE;
				if ( $user->save( ) ) {
					\Notification::push( 'You are account is activated ! ', \Notification::SUCCESS );
					\Supersoniq\Application::redirect_to_action( 'user', 'login' );
				}
			}
		}
		\Notification::push( 'There is a problem ! ', \Notification::ERROR );
		$this->view->title = 'Activation page';
		$this->view->content = ''; 
		return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
	}


	/*************************************************************************
	  EDITION ACTION                   
	 *************************************************************************/
	public function edit( ) {
		$this->view->title = 'Editer votre profil';
		$this->view->content = 'lorem ipsum'; 
		return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
	}
}

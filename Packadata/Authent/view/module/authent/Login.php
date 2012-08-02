<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Authent\View\Module\Authent;

class Login extends  Login\__Parent {



	/*************************************************************************
	  LOGIN ACTION                   
	 *************************************************************************/
	public function render( $parameters = [ ] ) {
		$controller = ( new \Controller )->by_type( 'Model\User' );
		
		// User already connected
		if ( $controller->is_logged( ) ) {
			header( 'Location: /' );
			die;
		}

		// Connection validation
		if ( isset( $_POST[ 'login' ] ) ) {
			if ( $controller->login( $_POST['login'], $_POST['password'] ) ) {
				header( 'Location: /' );
				die;
			}
		}

		return $this->get_template( );
	}
}

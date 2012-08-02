<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Authent\View\Module\Model\Edit;

class User extends User\__Parent {


	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
        public function fill( $template, $parameters = [ ] ) {
		if ( isset( $_POST[ 'model' ] ) ) {
			if ( isset( $_POST[ 'model' ][ 'password' ] ) ) {
				$user = ( new \Model )->by_type( 'User' )->one( );
				$_POST[ 'model' ][ 'password' ] = $user->encrypt_password( $_POST[ 'model' ][ 'password' ] );
			}
		}
		return parent::fill( $template, $parameters );
	}

}

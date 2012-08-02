<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Authent\View\Module\Authent;

class Logout extends  Logout\__Parent {



	/*************************************************************************
	  LOGIN ACTION                   
	 *************************************************************************/
	public function render( $parameters = [ ] ) {
		$controller = ( new \Controller )->by_type( 'Model\User' )->logout( );
		header( 'Location: /' );
		die;
	}
}

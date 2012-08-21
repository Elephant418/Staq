<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Authent\View\Module\Authent;

class Recover_Admin extends Recover_Admin\__Parent {



	/*************************************************************************
	  LOGIN ACTION                   
	 *************************************************************************/
	public function render( $parameters = [ ] ) {
		$controller = $this->get_controller( );
		if ( ! $controller->has_admin_user( ) ) {
			$model = $controller->get( );
			$controller->create_admin_user( );
		}
		\Supersoniq\redirect_to_module_page( 'Authent', 'login' );
	}
}

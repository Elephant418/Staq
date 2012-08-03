<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\View\Module\Model;

abstract class Erase extends Erase\__Parent {


	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
	public function render( $parameters = [ ] ) {
		$controller = $this->get_controller( );
		if ( isset( $parameters[ 'versions' ] ) ) {
			$model = $controller->erase( $parameters[ 'id' ], $parameters[ 'versions' ] );
		} else {
			$model = $controller->erase( $parameters[ 'id' ] );
		}
		\Supersoniq\redirect_to_page( 'all' );
	}

}

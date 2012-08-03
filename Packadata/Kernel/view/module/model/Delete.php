<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\View\Module\Model;

abstract class Delete extends Delete\__Parent {


	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
	public function render( $parameters = [ ] ) {
		$controller = $this->get_controller( );
		$model = $controller->get( $parameters[ 'id' ] );
		$controller->delete( $model );
		\Supersoniq\redirect_to_page( 'all' );
	}

}

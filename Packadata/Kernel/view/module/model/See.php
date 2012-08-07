<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\View\Module\Model;

abstract class See extends See\__Parent {


	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
    public function fill( $template, $parameters = [ ] ) {
		$template->archive = $this->get_controller( )->see( $parameters[ 'id' ],  $parameters[ 'versions' ] );
		$template->model = $template->archive->get_model( );
		return $template;
	}

}

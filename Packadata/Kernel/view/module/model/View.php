<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\View\Module\Model;

abstract class View extends View\__Parent {


	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
        public function fill( $template, $parameters = [ ] ) {
		$template->model = $this->get_controller( )->get( $parameters[ 'id' ] );
		return $template;
	}

}

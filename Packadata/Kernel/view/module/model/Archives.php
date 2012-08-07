<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\View\Module\Model;

abstract class Archives extends Archives\__Parent {


	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
	public function fill( $template, $parameters = [ ] ) {
		$template->archives = $this->get_controller( )->archives( $this->get_model_name( ) );
		return $template;
	}

}

<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\View\Module\Model;

abstract class Archive extends Archive\__Parent {


	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
    public function fill( $template, $parameters = [ ] ) {
		$template->archives = $this->get_controller( )->archive( $parameters[ 'id' ] );
		return $template;
	}

}
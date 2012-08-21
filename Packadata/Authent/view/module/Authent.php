<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Authent\View\Module;

class Authent extends  Authent\__Parent {



	/*************************************************************************
	  PROTECTED METHOD                   
	 *************************************************************************/
	protected function get_controller( ) {
		return ( new \Controller )->by_type( 'Model\User' );
	}
}

<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Qrumble\Object;

class Application extends Application\__Parent {



	/*************************************************************************
	  PRIVATE METHODS          
	 *************************************************************************/
	protected function call_module_page( $module_page ) {
		$template = parent::call_module_page( $module_page );
		if ( is_object( $template ) ) {
			return $template->compile( )->render( );
		}
		return $template;
	}
}

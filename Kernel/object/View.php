<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Object;

class View extends \Class_Type_Accessor {



	/*************************************************************************
	  CONSTRUCTOR                 
	 *************************************************************************/
	public function by_module_page( $module, $page ) {
		if ( is_object( $module ) ) {
			$module = $module->type;
		}
		return $this->by_name( 'Module\\' . $module . '\\' . ucfirst( $page ) );
	}

}

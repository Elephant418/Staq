<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Qrumble\Object;

class View extends \Class_Type_Accessor {



	/*************************************************************************
	  ACCESSOR                 
	 *************************************************************************/
	public function by_module_page( $module, $page ) {
		if ( is_object( $module ) ) {
			$module = $module->type;
		}
		return $this->by_name( 'Module\\' . $module . '\\' . ucfirst( $page ) );
	}

	public function by_layout( $layout ) {
		return $this->by_name( 'Layout\\' . ucfirst( $layout ) );
	}

}

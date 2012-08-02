<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Object;

class Controller extends \Class_Type_Accessor {



	/*************************************************************************
	  ACCESSOR                 
	 *************************************************************************/
	public function by_type( $name ) {
		return parent::by_name( $name );
	}

}

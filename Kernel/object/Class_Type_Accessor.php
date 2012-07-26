<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Object;

class Class_Type_Accessor {



	/*************************************************************************
	  CONSTRUCTOR                 
	 *************************************************************************/
	public function by_name( $name ) {
		$type = \Supersoniq\substr_after_last( get_class( $this ), '\\' );
		$class_name = ( new \Supersoniq\Kernel\Internal\Autoloader( ) )->load( $type, $name );
		return new $class_name;
	}
}

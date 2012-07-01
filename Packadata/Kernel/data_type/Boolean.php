<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type;

class Boolean extends \Data_Type\__Base {


	/*************************************************************************
	  DATABASE GETTER & SETTER             
	 *************************************************************************/
	public function value( ) {
		if ( $this->value ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function init( $value ) {
		if ( $value ) {
			$this->value = TRUE;
		} else {
			$this->value = FALSE;
		}
	}
}

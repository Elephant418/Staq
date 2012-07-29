<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type;

class Text extends \Data_Type\__Base {
	
	
	/*************************************************************************
	  USER GETTER & SETTER             
	 *************************************************************************/
	public function get( ) {
		return str_replace( PHP_EOL, '<br>', $this->value );
	}
}

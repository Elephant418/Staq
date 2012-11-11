<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type;

class Selection_Multiple extends Selection {
	
	
	/*************************************************************************
	  USER GETTER & SETTER             
	 *************************************************************************/
	public function get( ) {
		$values = $this->value;
		\Supersoniq\must_be_array( $values );
		$selections = [ ];
		foreach( $values as $value ) {
			if ( isset( $this->options[ $value ] ) ) {
				$selections[ ] = $this->options[ $value ];
			}
		}
		return implode( ', ', $selections );
	}
}

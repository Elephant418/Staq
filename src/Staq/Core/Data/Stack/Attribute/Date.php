<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute ;

class Date extends Date\__Parent {



	/*************************************************************************
	  PUBLIC USER METHODS             
	 *************************************************************************/
	public function get( ) {
		return \DateTime::createFromFormat( 'Y-m-d', $this->seed );
	}

	public function set( $value ) {
		if ( static::isValid( $value ) ) {
            $this->seed = $value;
		} else if ( is_a( $value, 'DateTime' ) ) {
			$this->seed = $value->format( 'Y-m-d' );
		}
	}

	public static function isValid( $value ) {
		if ( is_string( $value ) ) {
			if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $value, $matches ) ) {
		        if ( checkdate( $matches[ 2 ], $matches[ 3 ], $matches[ 1 ] ) ) {
		            return TRUE;
		        }
		    }
		}
	}
}
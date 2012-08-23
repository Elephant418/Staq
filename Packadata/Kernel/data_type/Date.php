<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type;

class Date extends \Data_Type\__Base {
	

	/*************************************************************************
	 ATTRIBUTES
	*************************************************************************/
	/*
	 * Regex from roscripts
	 * Date d/m/yy and dd/mm/yyyy
	 * 1/1/00 through 31/12/99 and 01/01/1900 through 31/12/2099
	 * Matches invalid dates such as February 31st
	 */
	private $regex = "*\b(0?[1-9]|[12][0-9]|3[01])[- /.](0?[1-9]|1[012])[- /.]((19|20)?[0-9]{2})\b*";
	

	/*************************************************************************
	 USER GETTER & SETTER
	*************************************************************************/
	public function set( $value ) {
		$matches = [ ];
		if ( preg_match( $this->regex, $value, $matches ) || $value == '' ) {
			if ( $matches ) {
				if ( strlen( $matches[ 1 ] ) == 1 ) {
					$matches[ 1 ] = '0' . $matches[ 1 ];
				}
				if ( strlen( $matches[ 2 ] ) == 1 ) {
					$matches[ 2 ] = '0' . $matches[ 2 ];
				}
				if ( strlen( $matches[ 3 ] ) == 2 ) {
					$matches[ 3 ] = '20' . $matches[ 3 ];
				}
				$value = $matches[ 1 ] . '-' . $matches[ 2 ] . '-' . $matches[ 3 ];
			}
			$this->init( $value );
		} else {
			\Notification::push( 'Wrong Input for the date ! Hasn\'t been saved. Must be DD-MM-YYYY.', \Notification::ERROR );
		}
	}


	/*************************************************************************
	 FORMATTERS
	*************************************************************************/
	/*
	 * function that allows to manipulate the format of the date
	 * @param the wanted type of format
	 * @return the value on the wanted format
	 */
	public function format( $type = NULL ) {
		if ( $type == 'Year' ) $formater = 'Y';
		else if ( $type == 'Month' ) $formater = 'Y-m';
		else if ( $type == 'Day' || is_null( $type ) ) $formater = 'Y-m-d';
		else $formater = $type;
		return $this->date( )->format( $formater );
	}

	public function date( ) {
		return \DateTime::createFromFormat( 'd-m-Y', $this->value );
	}

	public function time( ) {
		return $this->date( )->getTimestamp( );
	}
}

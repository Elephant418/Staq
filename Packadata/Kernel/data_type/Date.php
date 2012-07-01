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
	private $regex = "*\b(0?[1-9]|[12][0-9]|3[01])[- /.](0?[1-9]|1[012])[- /.](19|20)?[0-9]{2}\b*";
	protected $format;
	

	/*************************************************************************
	 USER GETTER & SETTER
	*************************************************************************/
	public function set( $value ) {
		if ( preg_match( $this->regex, $value ) || $value == '' ) {
			$this->init( $value );
		} else {
			\Notification::push( 'Wrong Input for the date ! Hasn\'t been saved.', \Notification::NOTICE );
			//TODO Interrompre totalement l'entrée en base
		}
	}
	public function get_format( ) {
		return $this->format;
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
		if ( $type != NULL ) {
			if ( $type == 'Year' )	{
				$this->format = $this->format_year( );
			} else if ( $type == 'Month' )	{
				$this->format = $this->format_month( );
			} else if ( $type == 'Day' ) {
				$this->format = $this->format_day( ); }
			else { 
				$this->format = date_format( \DateTime::createFromFormat( 'd/m/Y', $this->value ), $type );
			}
		} else {
			return $this->format = $this->format_day( );
		}
		return $this->format;
	}
	private function format_year( ) {
		return date_format( \DateTime::createFromFormat( 'd/m/Y', $this->value ), 'Y' );
	}
	private function format_month( ) {
		return date_format( \DateTime::createFromFormat( 'd/m/Y', $this->value ), 'Y/m' );
	}
	private function format_day( ) {
		return date_format( \DateTime::createFromFormat( 'd/m/Y', $this->value ), 'Y/m/d' );
	}
}

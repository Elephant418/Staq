<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type;

class Selection extends \Data_Type\__Base {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public $options = [ ];



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $options = [ ] ) {
		parent::__construct( );
		$this->options = $options;
	}
	
	
	/*************************************************************************
	  USER GETTER & SETTER             
	 *************************************************************************/
	public function get( ) {
		if ( isset( $this->options[ $this->value ] ) ) {
			return $this->options[ $this->value ];
		}
	}
}

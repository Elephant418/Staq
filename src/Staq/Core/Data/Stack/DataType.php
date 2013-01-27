<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack ;

class DataType {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $seed;



	/*************************************************************************
	  CONSTRUCTOR            
	 *************************************************************************/
	public function __construct( ) {
	}

	public function by_setting( $setting ) {
		$class = 'Stack\\DataType\\';
		if ( is_string( $setting ) ) {
			$class .= ucfirst( $setting );
		}
		return new $class;
	}



	/*************************************************************************
	  PUBLIC USER METHODS             
	 *************************************************************************/
	public function get( ) {
		return $this->seed;
	}

	public function set( $value ) {
		$this->seed = $value;
	}


	/*************************************************************************
	  PRIVATE DATABASE METHODS             
	 *************************************************************************/
	public function get_seed( ) {
		return $this->seed;
	}
	public function set_seed( $seed ) {
		$this->seed = $seed;
	}



	/*************************************************************************
	  DEBUG METHODS             
	 *************************************************************************/
	public function __toString( ) {
		return \Staq\Util::stack_query( $this ) . '( ' . $this->value . ' )';
	}
}
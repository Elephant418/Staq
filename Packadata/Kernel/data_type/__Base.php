<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type;

abstract class __Base {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public $type;
	public $name;
	public $scope = 'set';
	protected $value;


	/*************************************************************************
	  USER GETTER & SETTER             
	 *************************************************************************/
	public function __toString( ) {
		return $this->get( );
	}
	public function get( ) {
		return $this->value( );
	}
	public function set( $value ) {
		$this->init( $value );
		return $this;
	}
	public function set_name( $name ) {
		$this->name = $name;
		return $this;
	}
	public function get_data_type( ) {
		return $this->type;
	}


	/*************************************************************************
	  DATABASE GETTER & SETTER             
	 *************************************************************************/
	public function value( ) {
		return $this->value;
	}
	public function init( $value ) {
		$this->value = $value;
		return $this;
	}


	/*************************************************************************
	  MODEL EVENT METHODS             
	 *************************************************************************/
	public function model_initialized( $model ) {
	}
	public function model_saved( $model ) {
	}
	public function model_deleted( $model ) {
	}



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( ) {
		$this->type = \Supersoniq\substr_after_last( get_class( $this ), '\\' );
	}
}

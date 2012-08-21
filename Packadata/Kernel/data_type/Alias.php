<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type;

class Alias extends \Data_Type\__Base {



	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public $model;
	public $alias_type;
	protected $provider;


	/*************************************************************************
	  USER GETTER & SETTER             
	 *************************************************************************/
	public function get( ) {
		$provider = $this->provider;
		return $provider( $this->model );
	}
	public function get_data_type( ) {
		return $this->alias_type;
	}


	/*************************************************************************
	  NOT AUTHORIZED METHOD             
	 *************************************************************************/
	public function value( ) {
		throw new \Exception( 'No database value for data_type alias "' . $this->name . '"' );
	}
	public function init( $value ) {
		throw new \Exception( 'No setter for data_type alias "' . $this->name . '"' );
	}
	public function set( $value ) {
		throw new \Exception( 'No setter for data_type alias "' . $this->name . '"' );
	}


	/*************************************************************************
	  MODEL EVENT METHODS             
	 *************************************************************************/
	public function model_initialized( $model ) {
		$this->model = $model;
	}



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $provider, $data_type = 'Varchar' ) {
		$this->provider = $provider;
		$this->alias_type = $data_type;
		parent::__construct( );
	}
}

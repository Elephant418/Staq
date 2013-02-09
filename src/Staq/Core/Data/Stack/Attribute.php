<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack ;

class Attribute implements \Stack\IAttribute {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $seed;



	/*************************************************************************
	  CONSTRUCTOR            
	 *************************************************************************/
	public function by_setting( $model, $setting ) {
		$class = 'Stack\\Attribute';
		if ( is_string( $setting ) ) {
			$class .= '\\' . ucfirst( $setting );
		} else if ( is_array( $setting ) && isset( $setting[ 'attribute' ] ) ) {
			$class .= '\\' . ucfirst( $setting[ 'attribute' ] );
		}
		if ( strtolower( $class ) != strtolower( get_class( $this ) ) ) {
			return ( new $class )->by_setting( $model, $setting );
		}
		$this->init_by_setting( $model, $setting );
		return $this;
	}
	public function init_by_setting( $model, $setting ) {
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
	  PUBLIC DATABASE METHODS             
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
		return \Staq\Util::stack_query( $this ) . '( ' . $this->seed . ' )';
	}
}
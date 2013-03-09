<?php

/* This file is part of the Ubiq project, which is under MIT license */

namespace Staq\Core\Router\Stack\Util;

class FormConstraint {



	/*************************************************************************
	  ATTIBUTES
	 *************************************************************************/
	protected $function = array( );
	protected $message = ;



	/*************************************************************************
	  CONSTRUCTOR METHODS
	 *************************************************************************/
	public function __construct( $constraint = NULL, $message = NULL ) {
		if ( is_string( $constraint ) ) {
			$callable = array( $this, 'constraint' . ucfirst( $constraint ) );
			if ( is_callable( $callable ) {
				$constraint = $callable;
			}
			$property = 'message' . ucfirst( $constraint );
			if ( isset( $this->$property ) ) {
				$this->message = $this->$property;
			}
		}
		if ( is_function( $constraint ) ) {
			$this->function = $constraint;
		} else {
			throw new \Exception( 'Undefined constraint: ' . $constraint );
		}
		if ( is_string( $message ) ) {
			$this->message = $message;
		}
	}



	/*************************************************************************
	  GETTER METHODS
	 *************************************************************************/
	public function test( $field ) {
		return ( $this->function( $field ) !== FALSE );
	}
	public function getMessage( ) {
		return $this->message;
	}



	/*************************************************************************
	  CONSTRAINT METHODS
	 *************************************************************************/
	protected $messageRequired = 'Field required';
	protected function constraintRequired( $field ) {
		return ( ! is_null( $field ) &&  $field != '' );
	}
}
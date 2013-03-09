<?php

/* This file is part of the Ubiq project, which is under MIT license */

namespace Staq\Core\Router\Stack\Util;

class FormConstraint {



	/*************************************************************************
	  ATTIBUTES
	 *************************************************************************/
	protected $function = array( );
	protected $message;



	/*************************************************************************
	  CONSTRUCTOR METHODS
	 *************************************************************************/
	public function __construct( $constraint = NULL, $message = NULL ) {
		if ( is_string( $constraint ) ) {
			$property = 'message' . ucfirst( $constraint );
			if ( isset( $this->$property ) ) {
				$this->message = $this->$property;
			}
			$callable = array( $this, 'constraint' . ucfirst( $constraint ) );
			if ( is_callable( $callable ) ) {
				$constraint = $callable;
			}
		}
		if ( is_callable( $constraint ) ) {
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
		$anonymous = $this->function;
		return ( $anonymous( $field ) !== FALSE );
	}
	public function getMessage( ) {
		return $this->message;
	}



	/*************************************************************************
	  CONSTRAINT METHODS
	 *************************************************************************/
	protected $messageRequired = 'This field is required';
	protected function constraintRequired( $value ) {
		return ( ! is_null( $value ) &&  $value != '' );
	}

	protected $messageValidUrl = 'This field must be a valid url';
	protected function constraintValidUrl( $value ) {
		return filter_var( $value, FILTER_VALIDATE_URL );
	}
}
<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Ground\Stack;

class Exception extends \Exception {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $default_message = NULL;
	protected $default_code    = 0;



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $message = NULL, $code = NULL, \Exception $previous = NULL ) {
		if ( is_null( $message ) ) $message = $this->default_message;
		if ( is_null( $message ) ) $message = \Staq\Util\stack_sub_query_text( $this );
		if ( is_null( $code ) )    $code    = $this->default_code;
		parent::__construct( $message, $code, $previous );
	}
	public function from_previous( \Exception $previous ) {
		$class = get_class( $this );
		return new $class( NULL, NULL, $previous );
	}



	/*************************************************************************
	  PUBLIC METHODS                   
	 *************************************************************************/
	public function get_message( ) {
		return $this->getMessage( );
	}
	public function get_code( ) {
		return $this->getCode( );
	}
}

?>
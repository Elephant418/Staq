<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack\Exception;

class Resource_Not_Found extends Resource_Not_Found\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $default_code    = 404;



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function by_uri( $uri = NULL ) {
		$this->message = 'Resource not found for the uri "' . $uri . '"';
		return $this;
	}

	public function by_exception( $exception = NULL ) {
		$this->message = 'Resource not found for the ' . $exception;
		return $this;
	}
}

?>
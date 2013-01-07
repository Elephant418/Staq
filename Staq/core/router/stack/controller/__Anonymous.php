<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack\Controller;

class __Anonymous extends __Anonymous\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $callable;



	/*************************************************************************
	  CONSTRUCTOR             
	 *************************************************************************/
	public function __construct( $uri, $callable ) {
		$this->routes = [ new \Stack\Route( $callable, $uri ) ];
	}
}

?>
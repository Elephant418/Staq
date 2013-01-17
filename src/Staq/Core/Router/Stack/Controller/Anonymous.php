<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack\Controller;

class Anonymous extends Anonymous\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $callable;



	/*************************************************************************
	  CONSTRUCTOR             
	 *************************************************************************/
	public function __construct( $match_uri, $callable ) {
		$this->routes = [ new \Stack\Route( $callable, $match_uri ) ];
	}
}

?>

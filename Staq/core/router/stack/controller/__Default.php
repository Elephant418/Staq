<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack\Controller;

class __Default implements \Stack\IController {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $routes = [ ];



	/*************************************************************************
	  GETTER
	 *************************************************************************/
	public function get_routes( ) {
		return $this->routes;
	}



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function action( $parameters, $action ) {
	}

}

?>
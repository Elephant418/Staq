<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack;

class Controller implements \Stack\IController {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $routes = [ ];
	public static $setting = [ ];



	/*************************************************************************
	  GETTER
	 *************************************************************************/
	public function get_routes( ) {
		return $this->routes;
	}



	/*************************************************************************
	  CONSTRUCTOR             
	 *************************************************************************/
	public function __construct( ) {
		$this->initialize_routes( );
	}



	/*************************************************************************
	  PROTECTED METHODS             
	 *************************************************************************/
	protected function initialize_routes( ) {
		$setting = ( new \Stack\Setting )->parse( $this );
		foreach ( $setting->get_as_array( 'route' ) as $action => $param ) {
			$this->routes[ ] = ( new \Stack\Route )->by_attribute( $this, $action, $param );
		}
	}
}

?>
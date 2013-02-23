<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack;

class Application extends Application\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $router;
	protected $controllers = [ ];



	/*************************************************************************
	  GETTER             
	 *************************************************************************/
	public function get_controller( $name ) {
		return $this->router->get_controller( $name );
	}

	public function get_uri( $controller, $action, $parameters ) {
		return $this->router->get_uri( $controller, $action, $parameters );
	}

	public function get_current_uri( ) {
		return $this->router->get_current_uri( );
	}

	public function get_last_exception( ) {
		return $this->router->get_last_exception( );
	}
	


	/*************************************************************************
	  SETTER             
	 *************************************************************************/
	public function addController( $uri, $controller ) {
		$this->controllers[ ] = func_get_args( );
		return $this;
	}




	/*************************************************************************
	  INITIALIZATION            
	 *************************************************************************/
	public function initialize( ) {
		parent::initialize( );
		$this->router = new \Stack\Router( );
		if ( isset( $_SERVER[ 'REQUEST_URI' ] ) ) {
			$uri = \UString::substr_before( $_SERVER[ 'REQUEST_URI' ], '?' );
			\UString::do_not_start_with( $uri, $this->base_uri );
			\UString::do_start_with( $uri, '/' );
			$this->router->set_uri( $uri );
		}
	}




	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function run( ) {
		$this->router->initialize( $this->controllers );
		$this->controllers = [ ];
		echo $this->router->resolve( );
	}
}

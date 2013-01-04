<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack ;

class Router {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $controllers = [ ];
	protected $exceptions  = [ ];
	protected $routes      = [ ];
	protected $uris        = [ ];



	/*************************************************************************
	  GETTER             
	 *************************************************************************/
	public function get_current_uri( ) {
		return end( $this->uris );
	}



	/*************************************************************************
	  SETTER          
	 *************************************************************************/
	public function change_uri( $uri ) {
		if ( in_array( $uri, $this->uris ) ) {
			throw new \Exception( 'Redirecting loop detected' );
		}
		$this->uris[ ] = $uri;
		return $this;
	}



	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function __construct( $controllers ) {
		// TODO: Add enabled controllers
		// TODO: Add global route
		$class = new \ReflectionClass( 'Stack\\Controller\\__Anonymous' );
		foreach ( $controllers as $controller ) {
			$this->controllers[ ] = $class->newInstanceArgs( $controller );
		}
		foreach ( $this->controllers as $controller ) {
			$this->routes = array_merge( $this->routes, $controller->get_routes( ) );
		}
	}



	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function resolve( $uri ) {
		$this->change_uri( $uri );
		return $this->render( );
	}



	/*************************************************************************
	  PRIVATE METHODS             
	 *************************************************************************/
	protected function render( $exception = NULL ) {
		try {
			$active_route = $this->get_active_route( $exception );
			return $active_route->call_action( );
		} catch( \Exception $exception ) {
			$this->prevent_exception_boucle( $exception );
			return $this->render( $exception );
		}
	}

	protected function get_active_route( $exception ) {
		if ( is_null( $exception ) ) {
			$uri = $this->get_current_uri( );
			return $this->get_active_route_by_uri( $uri );
		} else {
			return $this->get_active_route_by_exception( $exception );
		}
	}

	protected function get_active_route_by_uri( $uri ) {
		foreach ( $this->routes as $route ) {
			if ( $route->match_uri( $uri ) ) {
				return $route;
			}
		}
		throw ( new \Stack\Exception\Resource_Not_Found )->by_uri( $uri );
	}

	protected function get_active_route_by_exception( $exception ) {
		foreach ( $this->routes as $route ) {
			if ( $route->match_exception( $exception ) ) {
				return $route;
			}
		}
		throw ( new \Stack\Exception\Resource_Not_Found )->by_exception( $exception );
	}

	protected function prevent_exception_boucle( $exception ) {
		if ( \Staq\Util\is_stack( $exception ) ) {			
			$name = \Staq\Util\stack_query( $exception );
		} else {
			$name = get_class( $exception );
		}
		if ( in_array( $name, $this->exceptions ) ) {
			throw new \Exception( 'Uncatched exception "' . $exception->getMessage( ) . '"' );
		}
		$this->exceptions[ ] = $name;
	}
}
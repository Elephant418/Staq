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
	protected $setting;



	/*************************************************************************
	  GETTER             
	 *************************************************************************/
	public function get_current_uri( ) {
		return end( $this->uris );
	}

	public function get_last_exception( ) {
		return end( $this->exceptions );
	}



	/*************************************************************************
	  SETTER          
	 *************************************************************************/
	public function change_uri( $uri ) {
		if ( in_array( $uri, $this->uris, TRUE ) ) {
			throw new \Exception( 'Redirecting loop detected' );
		}
		$this->uris[ ] = $uri;
		return $this;
	}

	protected function add_routes( $routes ) {
		$this->routes = array_merge( $this->routes, $routes );
	}



	/*************************************************************************
	  CONSTRUCTOR             
	 *************************************************************************/
	public function __construct( $anonymous_controllers ) {
		$this->setting = ( new \Stack\Setting )->parse( $this );
		$this->initialize_controllers( );
		$this->initialize_anonymous_controllers( $anonymous_controllers );
	}
	protected function initialize_controllers( ) {
		$controllers = $this->setting[ 'router.controller' ];
		foreach ( $controllers as $controller_name ) {
			$controller_class = '\\Stack\Controller\\' . $controller_name;
			$controller = new $controller_class( );
			$this->add_routes( $controller->get_routes( ) );
		}
	}
	protected function initialize_anonymous_controllers( $anonymous_controllers ) {
		$class = new \ReflectionClass( 'Stack\\Controller\\Anonymous' );
		foreach ( $anonymous_controllers as $arguments ) {
			$anonymous = $class->newInstanceArgs( $arguments );
			$this->add_routes( $anonymous->get_routes( ) );
		}
	}


	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function resolve( $uri ) {
		$this->change_uri( $uri );
		return $this->resolve_current_uri( );
	}



	/*************************************************************************
	  PRIVATE METHODS             
	 *************************************************************************/
	protected function resolve_current_uri( $exception = NULL ) {
		try {
			$active_routes = $this->get_active_routes( $exception );
			foreach ( $active_routes as $route ) {
				$result = $route->call_action( );
				if ( ! is_null( $result ) ) {
					return $this->render( $result );
				}
			}
			$this->throw_404( $exception );
		} catch( \Exception $exception ) {
			$this->prevent_exception_boucle( $exception );
			return $this->resolve_current_uri( $exception );
		}
	}

	protected function render( $result ) {
		return $result;
	}

	protected function throw_404( $exception = NULL ) {
		if ( is_null( $exception ) ) {
			throw ( new \Stack\Exception\ResourceNotFound )->by_uri( $this->get_current_uri( ) );
		} else {
			throw ( new \Stack\Exception\ResourceNotFound )->by_exception( $exception );
		}
	}

	protected function get_active_routes( $exception = NULL ) {
		if ( is_null( $exception ) ) {
			$uri = $this->get_current_uri( );
			$active_routes = $this->get_active_route_by_uri( $uri );
		} else {
			$active_routes = $this->get_active_route_by_exception( $exception );
		}
		return $active_routes;
	}

	protected function get_active_route_by_uri( $uri ) {
		$active_routes = [ ];
		foreach ( $this->routes as $route ) {
			if ( $result = $route->is_route_catch_uri( $uri ) ) {
				if ( $result === TRUE ) {
					$active_routes[ ] = $route;
				} else {
					\Staq\Util::http_action_redirect( $result );
				}
			}
		}
		return $active_routes;
	}

	protected function get_active_route_by_exception( $exception ) {
		$active_routes = [ ];
		foreach ( $this->routes as $route ) {
			if ( $route->is_route_catch_exception( $exception ) ) {
				$active_routes[ ] = $route;
			}
		}
		return $active_routes;
	}

	protected function prevent_exception_boucle( $exception ) {
		if ( \Staq\Util::is_stack( $exception ) ) {			
			$name = \Staq\Util::stack_query( $exception );
		} else {
			$name = get_class( $exception );
		}
		if ( in_array( $name, $this->exceptions, TRUE ) ) {
			throw new \Exception( 'Uncatched exception "' . $exception->getMessage( ) . '"' );
		}
		$this->exceptions[ ] = $name;
	}
}
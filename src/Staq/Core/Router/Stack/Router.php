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
	public function getCurrentUri( ) {
		return end( $this->uris );
	}

	public function getLastException( ) {
		return end( $this->exceptions );
	}

	public function getController( $name ) {
		if ( isset( $this->controllers[ $name ] ) ) {
			return $this->controllers[ $name ];
		}
	}

	public function get_route( $controller, $action ) {
		if ( isset( $this->routes[ $controller ][ $action ] ) ) {
			return $this->routes[ $controller ][ $action ];
		}
	}

	public function getUri( $controller, $action, $parameters ) {
		$route = $this->get_route( $controller, $action );
		if ( $route ) {
			return $route->getUri( $parameters );
		}
		return '#unknownroute';
	}



	/*************************************************************************
	  SETTER          
	 *************************************************************************/
	public function set_uri( $uri ) {
		if ( in_array( $uri, $this->uris, TRUE ) ) {
			throw new \Exception( 'Redirecting loop detected' );
		}
		$this->uris[ ] = $uri;
		return $this;
	}

	protected function add_routes( $controller_name, $routes, $prepend = FALSE ) {
		if ( $prepend ) {
			$this->routes = array_merge_recursive( [ $controller_name => $routes ], $this->routes );
		} else {
			$this->routes[ $controller_name ] = $routes;
		}
	}



	/*************************************************************************
	  CONSTRUCTOR             
	 *************************************************************************/
	public function initialize( $anonymous_controllers ) {
		$this->setting = ( new \Stack\Setting )->parse( $this );
		$this->initialize_controllers( );
		$this->initialize_anonymous_controllers( $anonymous_controllers );
	}
	protected function initialize_controllers( ) {
		$names = $this->setting[ 'controller' ];
		foreach ( array_reverse( $names ) as $controller_name ) {
			$routes = [ ];
			$controller_class = '\\Stack\Controller\\' . $controller_name;
			$controller = new $controller_class( );
			if ( 
				$this->setting[ 'mode' ] == 'global' ||
				$this->setting[ 'mode' ] == 'mixed'
			) {
				$selector = 'route.' . strtolower( str_replace( '\\', '_', $controller_name ) );
				foreach ( $this->setting->getAsArray( $selector ) as $action => $setting ) {
					$routes[ $action ] = ( new \Stack\Route )->bySetting( $controller, $action, $setting );
				}
			}
			if ( 
				$this->setting[ 'mode' ] == 'distributed' ||
				( $this->setting[ 'mode' ] == 'mixed' && empty( $routes ) )
			) {
				$routes = $controller->getRoutes( );
			}
			$this->add_routes( $controller_name, $routes );
			$this->controllers[ $controller_name ] = $controller;
		}
	}
	protected function initialize_anonymous_controllers( $anonymous_controllers ) {
		$class = new \ReflectionClass( 'Stack\\Controller\\Anonymous' );
		foreach ( $anonymous_controllers as $arguments ) {
			$anonymous = $class->newInstanceArgs( $arguments );
			$this->add_routes( 'Anonymous', $anonymous->getRoutes( ), TRUE );
		}
	}


	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function resolve( ) {
		return $this->resolve_current_uri( );
	}



	/*************************************************************************
	  PRIVATE METHODS             
	 *************************************************************************/
	protected function resolve_current_uri( $exception = NULL ) {
		try {
			$active_routes = $this->get_active_routes( $exception );
			foreach ( $active_routes as $controller => $routes ) {
				foreach ( $routes as $action => $route ) {
					$result = $this->call_controller( $controller, $action, $route );
					if ( $result === TRUE ) {
						return NULL;
					} else if ( ! is_null( $result ) ) {
						return $this->render( $result );
					}
				}
			}
			$this->throw_404( $exception );
		} catch( \Exception $exception ) {
			$this->prevent_exception_boucle( $exception );
			return $this->resolve_current_uri( $exception );
		}
	}

	protected function call_controller( $controller, $action, $route ) {
		return $route->callAction( );
	}

	protected function render( $result ) {
		return $result;
	}

	protected function throw_404( $exception = NULL ) {
		if ( is_null( $exception ) ) {
			throw ( new \Stack\Exception\ResourceNotFound )->byUri( $this->getCurrentUri( ) );
		} else {
			throw ( new \Stack\Exception\ResourceNotFound )->byException( $exception );
		}
	}

	protected function get_active_routes( $exception = NULL ) {
		if ( is_null( $exception ) ) {
			$uri = $this->getCurrentUri( );
			$active_routes = $this->get_active_routes_byUri( $uri );
		} else {
			$active_routes = $this->get_active_routes_byException( $exception );
		}
		return $active_routes;
	}

	protected function get_active_routes_byUri( $uri ) {
		$active_routes = [ ];
		foreach ( $this->routes as $controller => $routes ) {
			foreach ( $routes as $action => $route ) {
				if ( $result = $route->isRouteCatchUri( $uri ) ) {
					if ( $result === TRUE ) {
						if ( ! isset( $active_routes[ $controller ] ) ) {
							$active_routes[ $controller ] = [ ];
						}
						$active_routes[ $controller ][ $action ] = $route;
					} else {
						\Staq\Util::httpRedirectUri( $result );
					}
				}
			}
		}
		return $active_routes;
	}

	protected function get_active_routes_byException( $exception ) {
		$active_routes = [ ];
		foreach ( $this->routes as $controller => $routes ) {
			foreach ( $routes as $action => $route ) {
				if ( $route->isRouteCatchException( $exception ) ) {
					if ( ! isset( $active_routes[ $controller ] ) ) {
						$active_routes[ $controller ] = [ ];
					}
					$active_routes[ $controller ][ $action ] = $route;
				}
			}
		}
		return $active_routes;
	}

	protected function prevent_exception_boucle( $exception ) {
		if ( \Staq\Util::isStack( $exception ) ) {			
			$name = \Staq\Util::getStackQuery( $exception );
		} else {
			$name = get_class( $exception );
		}
		if ( in_array( $name, $this->exceptions, TRUE ) ) {
			throw new \Exception( 'Uncatched exception "' . $exception->getMessage( ) . '"' );
		}
		$this->exceptions[ ] = $name;
	}
}
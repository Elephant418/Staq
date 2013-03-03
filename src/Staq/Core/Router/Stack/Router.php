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

	public function getRoute( $controller, $action ) {
		if ( isset( $this->routes[ $controller ][ $action ] ) ) {
			return $this->routes[ $controller ][ $action ];
		}
	}

	public function getUri( $controller, $action, $parameters ) {
		$route = $this->getRoute( $controller, $action );
		if ( $route ) {
			return $route->getUri( $parameters );
		}
		return '#unknownroute';
	}



	/*************************************************************************
	  SETTER          
	 *************************************************************************/
	public function setUri( $uri ) {
		if ( in_array( $uri, $this->uris, TRUE ) ) {
			throw new \Exception( 'Redirecting loop detected' );
		}
		$this->uris[ ] = $uri;
		return $this;
	}

	protected function addRoutes( $controllerName, $routes, $prepend = FALSE ) {
		if ( $prepend ) {
			$this->routes = array_merge_recursive( [ $controllerName => $routes ], $this->routes );
		} else {
			$this->routes[ $controllerName ] = $routes;
		}
	}



	/*************************************************************************
	  CONSTRUCTOR             
	 *************************************************************************/
	public function initialize( $anonymousControllers ) {
		$this->setting = ( new \Stack\Setting )->parse( $this );
		$this->initializeControllers( );
		$this->initializeAnonymousControllers( $anonymousControllers );
	}
	protected function initializeControllers( ) {
		$names = $this->setting[ 'controller' ];
		foreach ( array_reverse( $names ) as $controllerName ) {
			$routes = [ ];
			$controllerClass = '\\Stack\Controller\\' . $controllerName;
			$controller = new $controllerClass( );
			if ( 
				$this->setting[ 'mode' ] == 'global' ||
				$this->setting[ 'mode' ] == 'mixed'
			) {
				$selector = 'route.' . strtolower( str_replace( '\\', '_', $controllerName ) );
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
			$this->addRoutes( $controllerName, $routes );
			$this->controllers[ $controllerName ] = $controller;
		}
	}
	protected function initializeAnonymousControllers( $anonymousControllers ) {
		$class = new \ReflectionClass( 'Stack\\Controller\\Anonymous' );
		foreach ( $anonymousControllers as $arguments ) {
			$anonymous = $class->newInstanceArgs( $arguments );
			$this->addRoutes( 'Anonymous', $anonymous->getRoutes( ), TRUE );
		}
	}


	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function resolve( ) {
		return $this->resolveCurrentUri( );
	}



	/*************************************************************************
	  PRIVATE METHODS             
	 *************************************************************************/
	protected function resolveCurrentUri( $exception = NULL ) {
		try {
			$activeRoutes = $this->getActiveRoutes( $exception );
			foreach ( $activeRoutes as $controller => $routes ) {
				foreach ( $routes as $action => $route ) {
					$result = $this->callController( $controller, $action, $route );
					if ( $result === TRUE ) {
						return NULL;
					} else if ( ! is_null( $result ) ) {
						return $this->render( $result );
					}
				}
			}
			$this->throw404( $exception );
		} catch( \Exception $exception ) {
			$this->preventExceptionLoop( $exception );
			return $this->resolveCurrentUri( $exception );
		}
	}

	protected function callController( $controller, $action, $route ) {
		return $route->callAction( );
	}

	protected function render( $result ) {
		return $result;
	}

	protected function throw404( $exception = NULL ) {
		if ( is_null( $exception ) ) {
			throw ( new \Stack\Exception\ResourceNotFound )->byUri( $this->getCurrentUri( ) );
		} else {
			throw ( new \Stack\Exception\ResourceNotFound )->byException( $exception );
		}
	}

	protected function getActiveRoutes( $exception = NULL ) {
		if ( is_null( $exception ) ) {
			$uri = $this->getCurrentUri( );
			$activeRoutes = $this->getActiveRoutesByUri( $uri );
		} else {
			$activeRoutes = $this->getActiveRoutesByException( $exception );
		}
		return $activeRoutes;
	}

	protected function getActiveRoutesByUri( $uri ) {
		$activeRoutes = [ ];
		foreach ( $this->routes as $controller => $routes ) {
			foreach ( $routes as $action => $route ) {
				if ( $result = $route->isRouteCatchUri( $uri ) ) {
					if ( $result === TRUE ) {
						if ( ! isset( $activeRoutes[ $controller ] ) ) {
							$activeRoutes[ $controller ] = [ ];
						}
						$activeRoutes[ $controller ][ $action ] = $route;
					} else {
						\Staq\Util::httpRedirectUri( $result );
					}
				}
			}
		}
		return $activeRoutes;
	}

	protected function getActiveRoutesByException( $exception ) {
		$activeRoutes = [ ];
		foreach ( $this->routes as $controller => $routes ) {
			foreach ( $routes as $action => $route ) {
				if ( $route->isRouteCatchException( $exception ) ) {
					if ( ! isset( $activeRoutes[ $controller ] ) ) {
						$activeRoutes[ $controller ] = [ ];
					}
					$activeRoutes[ $controller ][ $action ] = $route;
				}
			}
		}
		return $activeRoutes;
	}

	protected function preventExceptionLoop( $exception ) {
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
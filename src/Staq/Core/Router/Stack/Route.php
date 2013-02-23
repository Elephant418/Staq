<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack ;

class Route {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $callable;
	protected $match_uri;
	protected $match_exceptions = [ ];
	protected $aliases_uri      = [ ];
	protected $parameters       = [ ];



	/*************************************************************************
	  CONSTRUCTOR            
	 *************************************************************************/
	public function __construct( $callable = NULL, $match_uri = NULL, $match_exceptions = [ ] , $aliases_uri = [ ] ) {
		\UArray::do_convert_to_array( $match_exceptions );
		\UArray::do_convert_to_array( $aliases_uri );
		$this->callable         = $callable;
		$this->match_uri        = $match_uri;
		$this->match_exceptions = $match_exceptions;
		$this->aliases_uri      = $aliases_uri;
	}
	
	public function by_setting( $controller, $action, $setting ) {
		\UString::do_start_with( $action, 'action_' );
		$callable = [ $controller, $action ];
		if ( ! is_callable( $callable ) ) {
			$message = get_class( $controller ) . '::' .$action . ' is not callable';
			throw new \Stack\Exception\NoCallable( $message );
		}
		$uri = NULL;
		$exceptions = [ ];
		$aliases = [ ];
		if ( is_array( $setting ) ) {
			$uri        = isset( $setting[ 'uri'        ] ) ? $setting[ 'uri'        ] : $uri;
			$exceptions = isset( $setting[ 'exceptions' ] ) ? $setting[ 'exceptions' ] : $exceptions;
			$aliases    = isset( $setting[ 'aliases'    ] ) ? $setting[ 'aliases'    ] : $aliases;
		} else if ( is_string( $setting ) ) {
			$uri = $setting;
		}
		return new $this( $callable, $uri, $exceptions, $aliases );
	}



	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function get_uri( $parameters ) {
		$uri = $this->match_uri;
		foreach ( $parameters as $name => $value ) {
			if ( ! is_numeric( $name ) ) {
				$uri = str_replace( ':' . $name, $value, $uri );
				unset( $parameters[ $name ] );
			}
		}
		ksort( $parameters );
		foreach ( $parameters as $value ) {
			$uri = preg_replace( '#^([^:]*):\w+#', '${1}' . $value, $uri );
		}
		$uri = preg_replace( '#\(([^):]*)\)#', '${1}', $uri ); 
		$uri = preg_replace( '#\([^)]*\)#', '', $uri ); 
		$uri = preg_replace( '#:(\w+)#', '', $uri );
		return $uri;
	}

	public function call_action( ) {
		if ( is_array( $this->callable ) ) {
			$reflection = new \ReflectionMethod( $this->callable[ 0 ], $this->callable[ 1 ] );
		} else {
			$reflection = new \ReflectionFunction( $this->callable );
		}
		$parameters = [ ];
		foreach( $reflection->getParameters( ) as $parameter ) {
			if ( ! $parameter->canBePassedByValue( ) ) {
				throw new \Stack\Exception\ControllerActionDefinition( 'A controller could not have parameter passed by reference' );
			}
			if ( isset( $this->parameters[ $parameter->name ] ) ) {
				$parameters[ ] = $this->parameters[ $parameter->name ];
			} else if ( $parameter->isDefaultValueAvailable( ) ) {
				$parameters[ ] = $parameter->getDefaultValue( );
			} else {
				throw new \Stack\Exception\ControllerActionDefinition( 'The current uri does not provide a value for the parameter "' . $parameter->name . '"' );
			}
		}
		return call_user_func_array( $this->callable, $parameters );
	}

	public function is_route_catch_uri( $uri ) {
		if ( $this->is_uri_match( $uri, $this->match_uri ) ) {
			return TRUE;
		}
		foreach ( $this->aliases_uri as $alias ) {
			if ( $this->is_uri_match( $uri, $alias ) ) {
				return $this->get_uri( $this->parameters );
			}
		}
		return FALSE;
	}

	public function is_route_catch_exception( $exception ) {
		$parameters = [ ];
		$result = FALSE;
		foreach ( $this->match_exceptions as $match_exception ) {
			if ( is_numeric( $match_exception ) ) {
				if( $exception->getCode( ) == $match_exception ) {
					$result = TRUE;
				}
			} else if ( \Staq\Util::isStack( $exception ) ) {
				if( \Staq\Util::stack_sub_query( $exception ) === $match_exception ) {
					$result = TRUE;
				}
			} else if( get_class( $exception ) === $match_exception ) {
				$result = TRUE;
			} 
		}
		if ( $result ) {
			$parameters = $this->get_parameter_from_exception( $exception );
		}
		$this->parameters = $parameters;
		return $result;
	}



	/*************************************************************************
	  PROTECTED METHODS             
	 *************************************************************************/
	protected function is_uri_match( $uri, $refer ) {
		$pattern = str_replace( [ '.', '+', '?' ],  [ '\.', '\+', '\?' ], $refer ); 
		$pattern = preg_replace( '#\*#', '.*', $pattern );
		$pattern = preg_replace( '#\(([^)]*)\)#', '(?:\1)?', $pattern ); 
		$pattern = preg_replace( '#\:(\w+)#', '(?<\1>\w+)', $pattern ); 
		$pattern = '#^' . $pattern . '/?$#';
		$parameters = [ ];
		$result = preg_match( $pattern, $uri, $parameters );
		if ( $result ) {
			foreach ( array_keys( $parameters ) as $key ) {
				if ( is_numeric( $key ) ) {
					unset( $parameters[ $key ] );
				}
			}
		} else {
			$parameters = [ ];
		}
		$this->parameters = $parameters;
		return $result;
	}

	protected function get_parameter_from_exception( $exception ) {
		$parameters = [ ];
		$parameters[ 'code' ]      = $exception->get_code( );
		$parameters[ 'exception' ] = $exception;
		if ( \Staq\Util::isStack( $exception ) ) {
			$parameters[ 'query' ] = \Staq\Util::stackQuery( $exception );
			$parameters[ 'name'  ] = \Staq\Util::stack_sub_query( $exception );
		} else {
			$parameters[ 'query' ] = get_class( $exception );
			$parameters[ 'name'  ] = get_class( $exception );
		}
		return $parameters;
	}



	/*************************************************************************
	  DEBUG METHODS             
	 *************************************************************************/
	public function __toString( ) {
		$str = 'Route( ' . $this->match_uri . ' => ';
		if ( is_array( $this->callable ) ) {
			$controller = $this->callable[ 0 ];
			if ( \Staq\Util::isStack( $controller ) ) {
				$str .= \Staq\Util::stack_sub_query( $controller );
			} else {
				$str .= \UObject::convert_to_class( $controller );
			}
			$str .= '::' . $this->callable[ 1 ];
		} else {
			$str .= 'anonymous';
		}
		return $str . ' )';
	}
}
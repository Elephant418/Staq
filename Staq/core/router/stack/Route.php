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
	protected $aliases_uri          = [ ];
	protected $parameters       = [ ];



	/*************************************************************************
	  CONSTRUCTOR            
	 *************************************************************************/
	public function __construct( $callable, $match_uri = NULL, $match_exceptions = [ ] , $aliases_uri = [ ] ) {
		$this->callable         = $callable;
		$this->match_uri        = $match_uri;
		$this->match_exceptions = $match_exceptions;
		$this->aliases_uri          = $aliases_uri;
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
			$uri = preg_replace( '#^([^:]*):\w+#', '${1}' . $value, $route );
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
				throw new \Stack\Exception\Controller_Definition( 'A controller could not have parameter passed by reference' );
			}
			if ( isset( $this->parameters[ $parameter->name ] ) ) {
				$parameters[ ] = $this->parameters[ $parameter->name ];
			} else if ( $parameter->isDefaultValueAvailable( ) ) {
				$parameters[ ] = $parameter->getDefaultValue( );
			} else {
				throw new \Stack\Exception\Controller_Definition( 'The current uri does not provide a value for the parameter "' . $parameter->name . '"' );
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
		return FALSE;
		throw new \Stack\Exception\Not_Implemented_Yet( __METHOD__ . ' not implemented yet' );
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
}
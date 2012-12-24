<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Object;

class Route {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	private $main_route;



	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function from_string( $main_route ) {
		$this->main_route = $main_route;
		return $this;
	}
	public function from_array( $route ) {
		if ( isset( $route[ 'restriction' ] ) ) {
			$arguments = explode( ':', $route[ 'restriction' ] );
			$method = 'restriction_' . array_shift( $arguments );
			if ( is_callable( [ $this, $method ] ) ) {
				if ( ! call_user_func_array( [ $this, $method ], $arguments ) ) {
					return FALSE;
				}
			}
		}
		if ( isset( $route[ 'main' ] ) ) {
			$this->main_route = $route[ 'main' ];
		}
		return $this;
	}
	public function add_prefix( $prefix ) {
		\Supersoniq\must_starts_with( $prefix, '/' );
		$this->main_route = $prefix . $this->main_route;
		return $this;
	}


	/*************************************************************************
	  GETTER METHODS                   
	 *************************************************************************/
	public function handle( $route ) {
		$parameters = [ ];
		if ( $this->match( $this->main_route, $route, $parameters ) ) {
			return $parameters;
		}
		return FALSE;
	}
	public function __toString( ) {
		return $this->to_string( );
	}
	public function to_string( $parameters = [ ] ) {
		$route = $this->main_route;
		foreach ( $parameters as $name => $value ) {
			if ( ! is_numeric ( $name ) ) {
				$route = str_replace( ':' . $name, $value, $route );
				unset( $parameters[ $name ] );
			}
		}
		ksort( $parameters );
		foreach ( $parameters as $value ) {
			$route = preg_replace( '#^([^:]*):\w+#', '${1}' . $value, $route );
		}
		$route = preg_replace( '#\(([^):]*)\)#', '${1}', $route ); 
		$route = preg_replace( '#\([^)]*\)#', '', $route ); 
		$route = preg_replace( '#:(\w+)#', '', $route );
		return $route;
	}
	protected function match( $route, $subject, &$matches ) {
		$route = str_replace( [ '.', '+', '?' ],  [ '\.', '\+', '\?' ], $route ); 
		$route = preg_replace( '#\(([^)]*)\)#', '(?:\1)?', $route ); 
		$route = preg_replace( '#\:(\w+)#', '(?<\1>\w+)', $route ); 
		$pattern = '#^' . $route . '/?$#';
		$result = preg_match( $pattern, $subject, $matches );
		if ( $result ) {
			foreach ( array_keys( $matches ) as $key ) {
				if ( is_numeric( $key ) ) {
					unset( $matches[ $key ] );
				}
			}
		}
		return $result;
	}
}


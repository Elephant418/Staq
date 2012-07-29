<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Controller;

abstract class __Base {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $view;
	public $type;
	protected $no_routes = FALSE;
	protected $handled_routes = [ ];


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {
		$this->type = \Supersoniq\class_type_name( $this );
		$view_class = '\View\\' . $this->type;
		$this->view = new $view_class( );
	}


	/*************************************************************************
	  ROUTE METHODS                   
	 *************************************************************************/
	public function handle_route( $route ) {

		// Route defined
		if ( ! empty( $this->handled_routes ) ) {
			foreach ( $this->handled_routes as $action => $handled_route ) {
				$parameters = [ ];
				if ( $this->route_match( $handled_route, $route, $parameters ) ) {
					return [ $action, $parameters ];
				}
			}

		// Automatic route
		} else if ( ! $this->no_routes ) {

			// Parse URI
			$route_parts = explode( '/', substr( $route, 1 ) );
			if ( count( $route_parts ) < 2 ) {
				return FALSE;
			}
			$controller_name = strtolower( $route_parts[ 0 ] );
			$action_name = strtolower( $route_parts[ 1 ] );
			$parameters = array_slice( $route_parts, 2 );

			// Verify controller
			if ( strtolower( $this->type ) != $controller_name ) {
				return FALSE;
			}

			// Verify action
			if ( ! is_callable( [ $this, $action_name ] ) ) {
				return FALSE;
			}
			return [ $action_name, $parameters ];
		}
	}
	public function get_action_route( $action, $parameters = [ ] ) {

		// Route defined
		if ( ! empty( $this->handled_routes ) ) {
			$route = $this->handled_routes[ $action ];
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

		// Automatic route
		} else if ( ! $this->no_routes ) {
			return $action . '/' . implode( '/', $parameters );
		}
	}
	protected function add_handled_route( $action, $route ) {
		if ( ! is_callable( [ $this, $action ] ) ) {
			throw new \Exception( 'Unknown "' . $method .'" method' );
		}
		$this->handled_routes[ $action ] = $route;
	}
	protected function route_match( $route, $subject, &$matches ) {
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


	/*************************************************************************
	  PROTECTED RENDER METHODS                   
	 *************************************************************************/
	protected function render( $template ) {
		return $this->view->render( $template );
	}
}


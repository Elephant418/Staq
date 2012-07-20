<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Object;

class Application {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	private $exceptions = [ ];
	private $routes     = [ ];


	/*************************************************************************
	  GETTER             
	 *************************************************************************/
	public function current_route( ) {
		return end( $this->routes );
	}


	/*************************************************************************
	  SETTER          
	 *************************************************************************/
	public function route( $route ) {
		if ( in_array( $route, $this->routes ) ) {
			throw new \Exception( 'Redirecting loop detected' );
		}
		$this->routes[ ] = $route;
		return $this;
	}


	/*************************************************************************
	  RENDER METHODS          
	 *************************************************************************/
	public function render( $exception = NULL ) {
		try {
			if ( is_null( $exception ) ) {
				$route = $this->current_route( );
				$module_side = $this->get_module_side_by_route( $route );
			} else {
				$module_side = $this->get_module_side_by_exception( $exception );
			}
			return $this->call_module_side( $module_side );
		} catch( \Exception $exception ) {
			$this->prenvent_exception_boucle( $exception );
			return $this->render( $exception );
		}
	}

	private function get_module_side_by_route( $route ) {
		foreach ( \Supersoniq::$MODULES as $module ) {
			if ( $callable = $module->handle_route( $route ) ) {
				return [ [ $module, $callable[ 0 ] ], $callable[ 1 ] ];
			}
		}
		throw new \Exception\Resource_Not_Found( );
	}

	private function get_module_side_by_exception( $exception ) {
		foreach ( \Supersoniq::$MODULES as $module ) {
			if ( $callable = $module->handle_exception( $exception ) ) {
				return [ [ $module, $callable[ 0 ] ], $callable[ 1 ] ];
			}
		}
		throw new \Exception\Resource_Not_Found( );
	}

	private function call_module_side( $module_side ) {
		\Supersoniq\must_be_array( $module_side[ 1 ] );
		return call_user_func_array( $module_side[ 0 ], $module_side[ 1 ] );
	}
	
	private function prenvent_exception_boucle( $exception ) {
		if ( isset( $exception->type ) ) {			
			$name = $exception->type;
		} else {
			$name = get_class( $exception );
		}
		if ( in_array( $name, $this->exceptions ) ) {
			throw new \Exception( 'Uncatched exception "' . $name . '"' );
		}
		$this->exceptions[ ] = $name;
	}
}

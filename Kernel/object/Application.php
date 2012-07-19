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
	private $current_module;
	private $routes  = [ ];


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
	public function render( ) {
		try {
			return $this->render_current_route( );
		} catch( \Exception\Redirect $e ) {
			\Notification::push( $e->getMessage( ), \Notification::EXCEPTION );
			$this->route( $e->route );
			return $this->render( );
		} catch( \Exception $e ) {
			\Notification::push( $e->getMessage( ), \Notification::EXCEPTION );
			$this->route( \Supersoniq\module_side_route( 'Error', 'view', [ 'code' => '500' ] ) );
			return $this->render( );
		}
	}

	public function render_current_route( ) {
		$current_route = $this->current_route( );
		foreach ( \Supersoniq::$MODULES as $module ) {
			if ( $callable = $module->handle_route( $current_route ) ) {
				$this->current_module = $module->type;
				return call_user_func_array( [ $module, $callable[ 0 ] ], $callable[ 1 ] );
			}
		}
		throw new \Exception\Redirect( \Supersoniq\module_side_route( 'Error', 'view', [ 'code' => '404' ] ), 'Route not found' );
	}
}

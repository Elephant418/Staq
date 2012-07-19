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
	private $modules  = array( );
	private $current_module;
	private $routes = array( );


	/*************************************************************************
	  CONSTRUCTOR          
	 *************************************************************************/
	public function load_modules( ) {
		$modules = ( new \Supersoniq\Kernel\Object\Settings )
			->by_file( 'application' )
			->get_list( 'modules' );
		foreach( $modules as $module_name ) {
			try {
				// TODO: Instanciate a real module
				$module = $module_name;
				$this->modules[ $module_name ] = $module;
			} catch ( \Exception $e ) {
				\Notification::push( $e->getMessage( ), \Notification::EXCEPTION );
				$this->route( $this->action_route( 'Error', 'view', array( 'code' => '500' ) ) );
			}
		}
		return $this;
	}
	public function render( ) {
		try {
			return $this->render_current_route( );
		} catch( \Exception\Redirect $e ) {
			\Notification::push( $e->getMessage( ), \Notification::EXCEPTION );
			$this->route( $e->route );
			return $this->render( );
		} catch( \Exception $e ) {
			\Notification::push( $e->getMessage( ), \Notification::EXCEPTION );
			$this->route( $this->action_route( 'Error', 'view', array( 'code' => '500' ) ) );
			return $this->render( );
		}
	}


	/*************************************************************************
	  ROUTING METHODS                   
	 *************************************************************************/
	public function current_route( ) {
		return end( $this->routes );
	}
	public function route( $route ) {
		if ( in_array( $route, $this->routes ) ) {
			throw new \Exception( 'Redirecting loop detected' );
		}
		$this->routes[ ] = $route;
		return $this;
	}
	public function render_current_route( ) {
		$current_route = strtolower( $this->current_route( ) );
		foreach ( $this->modules as $module ) {
			if ( $callable = $module->handle_route( $current_route ) ) {
				$this->current_module = $module->type;
				return call_user_func_array( array( $module, $callable[ 0 ] ), $callable[ 1 ] );
			}
		}
		throw new \Exception\Redirect( $this->action_route( 'Error', 'view', array( 'code' => '404' ) ), 'Route not found' );
	}


	/*************************************************************************
	  ACTION METHODS                   
	 *************************************************************************/
	public static function redirect( $route ) {
		header( 'HTTP/1.1 302 Moved Temporarily' );
		header( 'Location: ' . $route );
		die( );
	}
	public static function redirect_to_action( $module, $action, $parameters = array( ) ) {
		self::redirect( self::action_url( $module, $action, $parameters ) );
	}
	public static function action_url( $module, $action, $parameters = array( ) ) {
		return \Supersoniq::$BASE_URL . $this->action_route( $module, $action, $parameters );
	}
	public static function action_route( $module, $action, $parameters = array( ) ) {
		$module = $this->modules[ $module ];
		$route = $module->get_action_route( $action, $parameters );
		return $route;
	}
	public static function call_action( $module, $action, $parameters = array( ) ) {
		$module = $this->modules[ $module ];
		return call_user_func_array( array( $module, $action ), $parameters );
	}
}

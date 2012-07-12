<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq;

class Application {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $modules      = array( );
	public static $modules_path = array( );
	public static $controllers  = array( );
	public static $current_controller;
	private $routes = array( );


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {

		// Initial route
		$this->routes = array( SUPERSONIQ_REQUEST_URI );

		// Configuration file
		$configuration = new Configuration( 'application' );

		// Errors
		ini_set( 'display_errors', $configuration->get( 'errors', 'display_errors' ) );
		$level = $configuration->get( 'errors', 'error_reporting' );
		if ( ! is_numeric( $level ) ) {
			$level = constant( $level );
		}
		error_reporting( $level );

		// Service
		date_default_timezone_set( $configuration->get( 'service', 'timezone' ) );

		// Enabled modules
		$modules = array( $this->uniform_module_name( SUPERSONIQ_APPLICATION ) );
		$modules = array_merge( $modules, $configuration->get( 'modules', 'enabled' ) );
		foreach( $modules as $module ) {
			$module_name = $this->uniform_module_name( $module );
			$module_path = $this->uniform_module_path( $module );
			self::$modules[ ] = $module_name;
			self::$modules_path[ $module_name ] = SUPERSONIQ_ROOT_PATH . $module_path . '/';
		}

		// Enabled controllers
		$controllers = $configuration->get( 'controllers', 'enabled' );
		foreach( $controllers as $controller ) {
			try {
				$controller_class = '\\Controller\\' . $controller;
				self::$controllers[ $controller ] = new $controller_class( );
			} catch ( Exception $e ) {
				throw new Exception( 'We can not instanciate "' . $controller . '" controller', 0, $e );
			}
		}
	}


	/*************************************************************************
	  PUBLIC METHODS                   
	 *************************************************************************/
	public function render( ) {
		try {
			return $this->route( );
		} catch( \Exception\Redirect $e ) {
			\Notification::push( $e->getMessage( ), \Notification::EXCEPTION );
			$this->change_route( $e->route );
			return $this->render( );
		} catch( \Exception $e ) {
			\Notification::push( $e->getMessage( ), \Notification::EXCEPTION );
			$this->change_route( '/error/500' );
			return $this->render( );
		}
	}


	/*************************************************************************
	  STATIC METHODS                   
	 *************************************************************************/
	public static function redirect( $route ) {
		header( 'HTTP/1.1 302 Moved Temporarily' );
		header( 'Location: ' . $route );
		die( );
	}
	public static function redirect_to_action( $controller, $action, $parameters = array( ) ) {
		self::redirect( self::action_url( $controller, $action, $parameters ) );
	}
	public static function action_url( $controller, $action, $parameters = array( ) ) {
		$controller = self::$controllers[ $controller ];
		$route = $controller->get_action_route( $action, $parameters );
		return SUPERSONIQ_REQUEST_BASE_URL . $route;
	}
	public static function call_action( $controller, $action, $parameters = array( ) ) {
		$controller = self::$controllers[ $controller ];
		return call_user_func_array( array( $controller, $action ), $parameters );
	}


	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	private function uniform_module_name( $module ) {
		return str_replace( '/', '\\', $module );
	}
	private function uniform_module_path( $module ) {
		return str_replace( '\\', '/', $module );
	}
	private function current_route( ) {
		return end( $this->routes );
	}
	private function change_route( $route ) {
		if ( in_array( $route, $this->routes ) ) {
			throw new \Exception( 'Redirecting loop detected' );
		}
		$this->routes[ ] = $route;
	}
	private function route( ) {
		$current_route = strtolower( $this->current_route( ) );
		foreach ( self::$controllers as $controller ) {
			if ( $callable = $controller->handle_route( $current_route ) ) {
				self::$current_controller = $controller->type;
				return call_user_func_array( array( $controller, $callable[ 0 ] ), $callable[ 1 ] );
			}
		}
		throw new \Exception\Redirect( '/error/404', 'Route not found' );
	}
}

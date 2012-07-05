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
	public static $configuration;
	public static $modules = array( );
	public static $root_paths = array( );
	public static $current_controller;
	public static $controllers = array( );
	private $root_path;
	private $routes = array( );


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( $root_path = '../../' ) {
		$this->root_path = $root_path;

		// Initial route
		$route = urldecode( $_SERVER[ 'REQUEST_URI' ] );
		$route = substr_before( $route, '?' );
		if ( $route === '' ) {
			$route = '/';
		}
		$this->routes[ ] = $route;
	}


	/*************************************************************************
	  PUBLIC METHODS                   
	 *************************************************************************/
	public function configuration( $project_name, $configuration_file = NULL ) {

		// Configuration file
		if ( is_null( $configuration_file ) ) {
			$configuration_file = $this->root_path . $project_name . '/conf/application.ini';
		}
		self::$configuration = new Configuration( $configuration_file );

		// Enabled modules
		$modules = array_merge( array( $project_name ), self::$configuration->get( 'Modules', 'Enabled' ) );
		foreach( $modules as $module ) {
			$module_name = str_replace( '/', '\\', $module );
			$module_path = str_replace( '\\', '/', $module );
			self::$modules[ ] = $module_name;
			self::$root_paths[ $module_name ] = $this->root_path . $module_path . '/';
		}

		// Enabled controllers
		$controllers = self::$configuration->get( 'Controllers', 'Enabled' );
		foreach( $controllers as $controller ) {
			try {
				$controller_class = '\\Controller\\' . $controller;
				self::$controllers[ $controller ] = new $controller_class( );
			} catch ( Exception $e ) {
				throw new Exception( 'We can not instanciate "' . $controller . '" controller', 0, $e );
			}
		}
	}
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
	public function run( ) {
		echo $this->render( );
	}


	/*************************************************************************
	  STATIC METHODS                   
	 *************************************************************************/
	public static function redirect( $route ) {
		header( 'HTTP/1.1 302 Moved Temporarily' );
		header( 'Location: ' . $route );
		die();
	}
	public static function redirect_to_action( $controller, $action, $parameters = array( ) ) {
		self::redirect( self::action_url( $controller, $action, $parameters ) );
	}
	public static function action_url( $controller, $action, $parameters = array( ) ) {
		$controller = self::$controllers[ $controller ];
		$route = $controller->get_action_route( $action, $parameters );
		return $route;
	}


	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
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

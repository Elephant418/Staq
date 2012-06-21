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
	private $root_path;
	private $rootes = array( );
	// TODO: Supprimer le default controller !
	public $default_controller = 'welcome';


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
	}
	public function render( ) {
		try {
			return $this->route( );
		} catch( \Exception\Redirect $e ) {
			\Message::push( $e->getMessage( ), \Message::EXCEPTION );
			$this->change_route( $e->route );
			return $this->render( );
		/*} catch( \Exception $e ) {
			\Message::push( $e->getMessage( ), \Message::EXCEPTION );
			$this->change_route( '/error/view/500' );
			return $this->render( );*/
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
		$url_parts = explode( '/', substr( $this->current_route( ), 1 ) );

		// Controller
		if ( strlen( $this->current_route( ) ) == 1 ) {
			$controller_name = $this->default_controller;
		} else {
			$controller_name = $url_parts[ 0 ];
		}
		try {		
			$controller_name = '\\Controller\\' . ucfirst( $controller_name );
			$this->controller = new $controller_name( );
		} catch ( Exception $e ) {
			var_dump( $e );
			// throw new \Exception\Redirect( '/error/view/404', 'Unknown controller "' . $controller_name . '"' );
		}

		// Action
		if ( count( $url_parts ) < 2 ) {
			$this->action = $this->controller->action_method( );
		} else {
			$this->action = $this->controller->action_method( $url_parts[ 1 ] );
		}
		if ( ! is_callable( $this->callable_action( ) ) ) {
			throw new \Exception\Redirect( '/error/view/404', 'Unknown controller\'s action "' . $controller_name . '::' . $this->action .'"' );
		}

		$this->parameters = array_slice( $url_parts, 2 );
		return call_user_func_array( $this->callable_action( ), $this->parameters );
	}
	private function callable_action( ) {
		return array( $this->controller, $this->action );
	}
}

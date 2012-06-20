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
	public static $modules = array( );
	public static $root_paths = array( );
	private $root_path;
	private $rootes = array( );


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
			$configuration_file = $this->root_path . $project_name . '/conf/project.ini';
		}
		$conf = new Configuration( $configuration_file );

		// Enabled modules
		$modules = array_merge( array( $project_name ), $conf->get( 'Modules', 'Enabled' ) );
		foreach( $modules as $module ) {
			$module_name = str_replace( '/', '\\', $module );
			$module_path = str_replace( '\\', '/', $module );
			self::$modules[ ] = $module_name;
			self::$root_paths[ $module_name ] = $this->root_path . $module_path . '/';
		}
	}
	public function run( ) {
		echo 'bou';
	}
}

<?php

/* Todo MIT license
 */

namespace Staq;

class Application {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $platform;
	public static $extensions = [ ];
	private $path;
	private $controllers = [ ];



	/*************************************************************************
	  GETTER             
	 *************************************************************************/
	public function get_path( ) {
		return $this->path;
	}

	public function get_extensions( ) {
		return self::$extensions;
	}

	public function get_platform( ) {
		return self::$platform;
	}



	/*************************************************************************
	  INITIALIZATION             
	 *************************************************************************/
	public function __construct( $path = 'Staq/ground', $platform = 'prod' ) {
		$this->path = $path;
		self::$platform = $platform;
		self::$extensions = $this->find_extensions( );
	}



	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function start( ) {

	}

	public function add_controller( $controller ) {
		$this->controllers[ ] = $controller;
	}

	public function run( ) {
		$this->start( );
		// regarder si un controller d'application répond à l'url
		// regarder si des controllers d'extension répond à l'url
		// catcher les exceptions
		// Lever une erreur 404
	}



	/*************************************************************************
	  PARSE SETTINGS             
	 *************************************************************************/
	private function find_extensions( ) {
		$extensions = [ $this->path ];
		$this->find_extensions_recursive( $this->path, $extensions );
		var_dump( $extensions );
		return $extensions;
	}

	private function find_extensions_recursive( $extension, &$extensions ) {
		$setting_file_path = STAQ_ROOT_PATH . $extension . '/setting/application.ini';
		$added_extensions = [ ];
		if ( is_file( $setting_file_path ) ) {
			$settings = parse_ini_file( $setting_file_path, TRUE );
			if ( isset( $settings[ 'extensions' ][ 'enabled' ] ) && is_array( $settings[ 'extensions' ][ 'enabled' ] ) ) {
				$added_extensions = $settings[ 'extensions' ][ 'enabled' ];
			}
		} else {
			// Default value for extension without configuration 
			$added_extensions = [ 'Staq/ground' ];
		}
		$old_extensions = $extensions;
		$extensions = \Staq\util\array_reverse_merge_unique( $extensions, $added_extensions );
		foreach ( array_diff( $added_extensions, $old_extensions ) as $added_extension ) {
			$this->find_extensions_recursive( $added_extension, $extensions );
		}
	}

}

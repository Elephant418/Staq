<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

class Application {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $platform;
	public static $extensions = [ ];
	protected $path;
	protected $root_uri;
	protected $router;
	protected $controllers = [ ];



	/*************************************************************************
	  GETTER             
	 *************************************************************************/
	public function get_path( ) {
		return $this->path;
	}

	public function get_root_uri( ) {
		return $this->root_uri;
	}

	public function get_extensions( ) {
		return self::$extensions;
	}

	public function get_platform( ) {
		return self::$platform;
	}



	/*************************************************************************
	  SETTER             
	 *************************************************************************/
	public function add_controller( $uri, $controller ) {
		$this->controllers[ ] = func_get_args( );
		return $this;
	}



	/*************************************************************************
	  INITIALIZATION             
	 *************************************************************************/
	public function __construct( $path = 'Staq/core/ground', $root_uri = '/', $platform = 'prod' ) {
		$this->path       = $path;
		$this->root_uri  = $root_uri;
		self::$platform   = $platform;
		self::$extensions = $this->find_extensions( );
	}



	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function start( ) {
		$autoloader = new \Staq\Autoloader;
		spl_autoload_register( array( $autoloader, 'autoload' ) );
	}

	public function run( ) {
		$this->start( );
		$this->router = new \Stack\Router( $this->controllers );
		$uri          = \Staq\Util\string_substr_before( $_SERVER[ 'REQUEST_URI' ], '?' );
		echo $this->router->resolve( $uri );
	}



	/*************************************************************************
	  PARSE SETTINGS             
	 *************************************************************************/
	protected function find_extensions( ) {
		$extensions = [ $this->path ];
		$this->find_extensions_recursive( $this->path, $extensions );
		return $extensions;
	}

	protected function find_extensions_recursive( $extension, &$extensions, $disabled = [ ] ) {
		$added_extensions = $this->find_extensions_parse_settings_file( $extension, $disabled );
		$old_extensions = $extensions;
		$extensions = \Staq\Util\array_reverse_merge_unique( $extensions, $added_extensions );
		foreach ( array_diff( $added_extensions, $old_extensions ) as $added_extension ) {
			$this->find_extensions_recursive( $added_extension, $extensions, $disabled );
		}
	}

	protected function find_extensions_parse_settings_file( $extension, &$disabled ) {
		$added_extensions = [ ];
		$setting_file_path = STAQ_ROOT_PATH . $extension . '/setting/application.ini';
		if ( is_file( $setting_file_path ) ) {
			$settings = parse_ini_file( $setting_file_path, TRUE );
			if ( isset( $settings[ 'extensions' ] ) ) {
				$ext = $settings[ 'extensions' ];
				if ( isset( $ext[ 'enabled' ] ) && is_array( $ext[ 'enabled' ] ) ) {
					$added_extensions = array_diff( $ext[ 'enabled' ], $disabled );
				}
				if ( isset( $ext[ 'disabled' ] ) && is_array( $ext[ 'disabled' ] ) ) {
					$disabled = \Staq\Util\array_merge_unique( $disabled, $ext[ 'disabled' ] );
				}
			}
		} else {
			// Default value for extension without configuration 
			$added_extensions = [ 'Staq/core/ground' ];
		}
		return $added_extensions;
	}

}

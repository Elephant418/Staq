<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

class Supersoniq {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	static public $application;
	static public $APPLICATION_NAME;
	static public $PLATFORM_NAME;
	static public $BASE_URL;
	static public $EXTENSIONS = [ ];
	static public $MODULES    = [ ];
	private $applications     = [ ];
	private $platforms        = [ ];


	/*************************************************************************
	  SETTINGS METHODS                   
	 *************************************************************************/
	public function platform( $platform_name, $listenings = NULL ) {
		$this->format_listenings( $listenings );
		foreach( $listenings as $listening ) {
			$this->platforms[ ] = $this->format_platform( $platform_name, $listening );
		}
		return $this;
	}

	public function application( $application_path, $listenings = NULL ) {
		$this->format_application_path( $application_path );
		$this->format_listenings( $listenings );
		foreach( $listenings as $listening ) {
			$this->applications[ ] = $this->format_application( $application_path, $listening );
		}
		return $this;
	}

	private function format_listenings( &$listenings ) {
		$listenings = ( new \Supersoniq\Kernel\Url )->from( $listenings );
		\Supersoniq\must_be_array( $listenings );
		return $listenings;
	}  


	/*************************************************************************
	  RUN METHODS                   
	 *************************************************************************/
	public function run( $request = NULL ) {
		echo $this->render( $request );
		return $this;
	}
	public function render( $request = NULL ) {
		$this->format_request( $request );
		$this->initialize_attributes( $request );
		$this->initialize_settings( );
		self::$application = $this->instanciate_application( $request );
		return self::$application->render( );
	}



	/*************************************************************************
	  APPLICATION METHODS                   
	 *************************************************************************/
	private function instanciate_application( $request ) {
		$route = $this->route_by_request( $request );
		return ( new \Application )
			->route( $route );
	}

	private function route_by_request( $request ) {
		return \Supersoniq\substr_after( $request->to_string( ), self::$BASE_URL );
	}


	/*************************************************************************
	  SETTINGS METHODS                   
	 *************************************************************************/
	private function initialize_settings( ) {
		$settings = ( new \Supersoniq\Kernel\Object\Settings )
			->by_file( 'application' );
		$this->initialize_settings_error( $settings );
		$this->initialize_settings_timezone( $settings );
	}

	private function initialize_settings_error( $settings ) {
		ini_set( 'display_errors', 1 ); // $settings->get( 'errors', 'display_errors' ) );
		$level = $settings->get( 'errors', 'error_reporting', 0 );
		if ( ! is_numeric( $level ) ) {
			$level = constant( $level );
		}
		error_reporting( E_ALL ); // $level );
	}

	private function initialize_settings_timezone( $settings ) {
		date_default_timezone_set( $settings->get( 'service', 'timezone', 'Europe/Paris' ) );
	}

	private function activate_autoload( ) {
		( new \Supersoniq\Kernel\Internal\Autoloader )->init( );
	}


	/*************************************************************************
	  ATTRIBUTES METHODS                   
	 *************************************************************************/
	private function initialize_attributes( $request ) {
		$platform               = $this->platform_by_request( $request );
		$application            = $this->application_by_request( $request, $platform );
		self::$PLATFORM_NAME    = $platform[ 'name' ];
		self::$APPLICATION_NAME = \Supersoniq\format_to_namespace( $application[ 'path' ] );
		self::$BASE_URL         = $this->base_url_by_request( $request, $platform, $application );
		self::$EXTENSIONS       = $this->get_extensions( );
		$this->activate_autoload( );
		self::$MODULES          = $this->get_modules( );
	}

	private function format_request( &$request ) {
		if ( is_null( $request ) ) {
			$request = ( new \Supersoniq\Kernel\Url )->by_server( );
		} else {
			$request = ( new \Supersoniq\Kernel\Url )->from( $request );
		}
		return $request;
	}

	private function base_url_by_request( $request, $platform, $application ) {
		$base_url = clone $request;
		return $base_url
			->reset_uri( )
			->add_uri( $platform[ 'listening' ] )
			->add_uri( $application[ 'listening' ] )
			->to_string( );
	}

	private function get_extensions( ) {
		$settings = ( new \Supersoniq\Kernel\Object\Settings );
		$application_path = \Supersoniq\format_to_path( self::$APPLICATION_NAME );
		$extensions = [ $application_path ];
		do {
			$old = $extensions;
			$extensions = $settings
				->extension( $extensions )
				->by_file( 'application' )
				->get_list( 'extensions' );
			array_unshift( $extensions, $application_path );	
		} while ( $extensions != $old );
		return $extensions;
	}

	private function get_modules( ) {
		$modules = [ ];
		$module_names = ( new \Settings )
			->by_file( 'application' )
			->get_list( 'modules' );
		foreach( $module_names as $module_name ) {
			$module = ( new \Module )->by_name( $module_name );
			$modules[ $module_name ] = $module;
		}
		return $modules;
	}



	/*************************************************************************
	  PLATFORM METHODS                  
	 *************************************************************************/
	private function platform_by_request( $request ) {
		foreach ( $this->platforms as $platform ) {
			if ( $platform[ 'listening' ]->match( $request ) ) {
				return $platform;
			}
		}
		return $this->platform_default( );
	}

	private function platform_default( ) {
		return $this->format_platform( 'prod' );
	}

	private function format_platform( $name, $listening = NULL ) {
		return [ 
			'name'      => $name, 
			'listening' => $listening
		];
	}


	/*************************************************************************
	  APPLICATION METHODS                
	 *************************************************************************/
	private function application_by_request( $request, $platform ) {
		$unmatched = clone $request;
		$unmatched = $unmatched->diff( $platform[ 'listening' ] );
		foreach ( $this->applications as $application ) {
			if ( $application[ 'listening' ]->match( $unmatched ) ) {
				return $application;
			}
		}
		return $this->application_default( );
	}

	private function application_default( ) {
		$application_path = 'Supersoniq/Starter';
		$index_path = $_SERVER[ 'DOCUMENT_ROOT' ];
		if ( 
			\Supersoniq\ends_with( $index_path, '/public' ) && 
			\Supersoniq\starts_with( $index_path, SUPERSONIQ_ROOT_PATH )
		) {
			$application_path = \Supersoniq\substr_after( $index_path, SUPERSONIQ_ROOT_PATH );
			$application_path = \Supersoniq\substr_before_last( $application_path, '/public' );
		}
		return $this->format_application( $application_path );
	}

	private function format_application( $path, $listening = NULL ) {
		return [ 
			'path'      => $path, 
			'listening' => $listening
		];
	}

	private function format_application_path( &$application_path ) {
		$application_path = \Supersoniq\format_to_path( $application_path );
		return $application_path;
	}
}

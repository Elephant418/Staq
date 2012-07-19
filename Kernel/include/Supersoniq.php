<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

class Supersoniq {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	static public $APPLICATION;
	static public $PLATFORM;
	static public $BASE_URL;
	static public $EXTENSIONS = array( );
	private $route;
	private $applications = array( );
	private $platforms    = array( );


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
		// TODO: Instance a default error module

		$this->context_by_request( $request );
		$this->instanciate_application( );

		// TODO: Get enabled modules
		// TODO: Set errors
		// TODO: Instance an Application

		return $this;
	}

	public function context_by_request( $request ) {
		$this->format_request( $request );

		$platform       = $this->platform_by_request( $request );
		self::$PLATFORM = $platform[ 'name' ];
		unset( $this->platforms );

		$application       = $this->application_by_request( $request, $platform );
		self::$APPLICATION = $application[ 'path' ];
		unset( $this->applications );

		self:$BASE_URL = $this->base_url_by_request( $request, $platform, $application );
		$this->route   = $this->route_by_request( $request, $platform, $application );
	}

	public function instanciate_application( ) {
		self::$EXTENSIONS = $this->get_enabled_extensions( );
		( new \Supersoniq\Kernel\Autoloader )->init( );
		new \Application;
		print_r( self::$EXTENSIONS );
	}


	/*************************************************************************
	  APPLICATION METHODS                   
	 *************************************************************************/
	public function get_enabled_extensions( ) {
		$settings = ( new \Supersoniq\Kernel\Object\Settings );
		$extensions = array( self::$APPLICATION );
		do {
			$old = $extensions;
			$extensions = $settings
				->extension( $extensions )
				->by_file( 'application' )
				->get_list( 'extensions' );
			array_unshift( $extensions, self::$APPLICATION );	
		} while ( $extensions != $old );
		return $extensions;
	}


	/*************************************************************************
	  CONTEXT METHODS                   
	 *************************************************************************/
	private function format_request( &$request ) {
		if ( is_null( $request ) ) {
			$request = ( new \Supersoniq\Kernel\Url )->by_server( );
		} else {
			$request = ( new \Supersoniq\Kernel\Url )->from( $request );
		}
		return $request;
	}

	private function base_url_by_request( $request, $platform, $application ) {
		return $request
			->reset_uri( )
			->add_uri( $platform[ 'listening' ] )
			->add_uri( $application[ 'listening' ] )
			->to_string( );
	}

	private function route_by_request( $request, $platform, $application ) {
		return $request
			->diff_uri( $platform[ 'listening' ] )
			->diff_uri( $application[ 'listening' ] )
			->uri;
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
		$unmatched = $request->diff( $platform[ 'listening' ] );
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

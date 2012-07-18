<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

class Supersoniq {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $application_path;
	public $platform_name;
	public $route;
	private $applications = array( );
	private $platforms    = array( );


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {
		// TODO: Instance a default error module
	}


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
		if ( ! is_array( $listenings ) ) {
			$listenings = array( $listenings );
		}
		return $listenings;
	}  


	/*************************************************************************
	  RUN METHODS                   
	 *************************************************************************/
	public function run( $request = NULL ) {
		$this->context_by_request( $request );
		// $this->load_configuration( );
		// TODO: Instance an Application
	}

	public function context_by_request( $request ) {
		$this->format_request( $request );
		$platform    = $this->platform_by_request( $request );
		$application = $this->application_by_request( $request, $platform );
		$this->route = $this->route_by_request( $request, $platform, $application );
		$this->base_url         = $this->base_url_by_request( $request, $platform, $application );
		$this->platform_name    = $platform[ 'name' ];
		$this->application_path = $application[ 'path' ];
	}

	public function load_configuration( ) {
		$configuration = new \Supersoniq\Kernel\Object\Configuration( 'application' );
		// TODO: Iterate an enabled extensions with platform
		// TODO: Get enabled extensions
		// TODO: Get enabled modules
		// TODO: Set errors
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
		$application_path = str_replace( '\\', '/', $application_path );
		return $application_path;
	}
}

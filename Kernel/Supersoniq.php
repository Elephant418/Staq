<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

class Supersoniq {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $application;
	private $applications = array( );
	private $platforms    = array( );
	private $request_base_uri = '';
	private $request_uri;


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {

		// Initialize root path
		define( 'SUPERSONIQ_ROOT_PATH', \Supersoniq\dirname( __FILE__, 3 ) . '/' );

		// Original request 
		$this->request_uri = \Supersoniq\substr_before( $_SERVER[ 'REQUEST_URI' ], '?' );
	}


	/*************************************************************************
	  CONFIGURATION METHODS                   
	 *************************************************************************/
	public function application( $application, $listenings = '/' ) {
		$application_name = $this->uniform_module_name( $application );
		$this->applications[ $application_name ] = $listenings;
		return $this;
	}
	public function platform( $platform, $listenings = '/' ) {
		$this->platforms[ $platform ] = $listenings;
		return $this;
	}


	/*************************************************************************
	  RUN METHODS                   
	 *************************************************************************/
	public function run( ) {
		echo $this->render( );
	}
	public function render( ) {

		// Get current application & platform by the request
		define( 'SUPERSONIQ_PLATFORM'        , $this->select_platform( ) );
		define( 'SUPERSONIQ_APPLICATION'     , $this->select_application( ) );

		// Determine the part of the request is the uri
		define( 'SUPERSONIQ_REQUEST_BASE_URL', 'http://' . $this->request_base_url( ) );
		define( 'SUPERSONIQ_REQUEST_URI'     , $this->request_uri );
		// echo SUPERSONIQ_REQUEST_BASE_URL . ' - ' . SUPERSONIQ_REQUEST_URI . HTML_EOL;

		// Launch application
		self::$application = new \Supersoniq\Application( );
		return self::$application->render( );
	}


	/*************************************************************************
	  APPLICATION & PLATFORM LISTENING                  
	 *************************************************************************/
	private function select_platform( ) {
		foreach ( $this->platforms as $platform => $listenings ) {
			if ( $host = $this->handle_request( $listenings ) ) {
				return $platform;
			}
		}
	}
	private function select_application( ) {
		foreach ( $this->applications as $application => $listenings ) {
			if ( $this->handle_request( $listenings ) ) {
				return $application;
			}
		}
	}
	private function handle_request( $listenings ) {
		if ( ! is_array( $listenings ) ) {
			$listenings = array( $listenings );
		}
		foreach ( $listenings as $listening ) {
			$method_name = 'handle_request_by_uri';
			if ( \Supersoniq\starts_with( $listening, array( 'http://', 'https://', '//' ) ) ) {
				$method_name = 'handle_request_by_url';
			} else if ( \Supersoniq\starts_with( $listening, ':' ) ) {
				$method_name = 'handle_request_by_port';
			}
			if ( $this->$method_name( $listening ) ) {
				return TRUE;
			}
		}
		return FALSE;
	}
	private function handle_request_by_url( $listen_url ) {
		$base_request = $this->request_base_url( );
		$full_request = $base_request . $this->request_uri;
		$full_request = \Supersoniq\must_ends_with( $full_request, '/' );
		$listen_url   = \Supersoniq\substr_after( $listen_url, '//' );
		$listen_url   = \Supersoniq\must_not_ends_with( $listen_url, '/' );
		// echo $base_request . ' &lt; ' . $listen_url . ' &lt; ' . $full_request . HTML_EOL;
		if ( 
			\Supersoniq\starts_with( $listen_url  , $base_request ) && 
			\Supersoniq\starts_with( $full_request, $listen_url   )
		) {
			$this->request_base_uri .= \Supersoniq\substr_after( $listen_url, $base_request );
			$this->request_uri = \Supersoniq\substr_after( $full_request, $listen_url );
			return TRUE;
		}
		return FALSE;
	}
	private function handle_request_by_uri( $listen_uri ) {
		return $this->handle_request_by_url( '//' . $this->request_base_url( $listen_uri ) );
	}
	private function handle_request_by_port( $listen_port_uri ) {
		$listen_port_uri =       \Supersoniq\substr_after( $listen_port_uri, ':' );
		$listen_port     =      \Supersoniq\substr_before( $listen_port_uri, '/' );
		$listen_base_uri = '/' . \Supersoniq\substr_after( $listen_port_uri, '/' );
		return $this->handle_request_by_url( '//' . $this->request_base_url( $listen_base_uri, $listen_port ) );
	}
	private function request_base_url( $base_uri = NULL, $port = NULL ) {
		$request_base_url = $_SERVER[ 'SERVER_NAME' ];

		// Port
		if ( is_null( $port ) ) {
			$port = $_SERVER[ 'SERVER_PORT'];
		}
		if ( $port != '80' ) {
			$request_base_url .= ':' . $port;
		}
		
		// Base uri
		$base_uri = $this->request_base_uri . $base_uri;
		return $request_base_url . $base_uri;
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
}

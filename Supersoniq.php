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
		define( 'SUPERSONIQ_ROOT_PATH', dirname( dirname( __FILE__ ) ) . '/' );

		// Original request 
		$this->request_uri = \Supersoniq\substr_before( $_SERVER[ 'REQUEST_URI' ], '?' );
	}


	/*************************************************************************
	  CONFIGURATION METHODS                   
	 *************************************************************************/
	public function application( $application, $pattern = '/' ) {
		$application_name = $this->uniform_module_name( $application );
		$this->applications[ $application_name ] = $pattern;
		return $this;
	}
	public function platform( $platform, $pattern = '/' ) {
		$this->platforms[ $platform ] = $pattern;
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
		define( 'SUPERSONIQ_REQUEST_BASE_URL', $this->request_base_url( ) );
		define( 'SUPERSONIQ_REQUEST_URI'     , $this->request_uri );
		// echo SUPERSONIQ_REQUEST_BASE_URL . ' - ' . SUPERSONIQ_REQUEST_URI . HTML_EOL;

		// Launch application
		self::$application = new \Supersoniq\Application( );
		return self::$application->render( );
	}


	/*************************************************************************
	  URL PATTERN                  
	 *************************************************************************/
	private function select_platform( ) {
		foreach ( $this->platforms as $platform => $pattern ) {
			if ( $host = $this->is_pattern_match( $pattern ) ) {
				return $platform;
			}
		}
	}
	private function select_application( ) {
		foreach ( $this->applications as $application => $pattern ) {
			if ( $this->is_pattern_match( $pattern ) ) {
				return $application;
			}
		}
	}
	private function is_pattern_match( $patterns ) {
		if ( ! is_array( $patterns ) ) {
			$patterns = array( $patterns );
		}
		foreach ( $patterns as $pattern ) {
			$method_name = 'is_pattern_match_by_uri';
			if ( \Supersoniq\starts_with( $pattern, array( 'http://', 'https://', '//' ) ) ) {
				$method_name = 'is_pattern_match_by_url';
			} else if ( \Supersoniq\starts_with( $pattern, ':' ) ) {
				$method_name = 'is_pattern_match_by_port';
			}
			if ( $this->$method_name( $pattern ) ) {
				return TRUE;
			}
		}
		return FALSE;
	}
	private function is_pattern_match_by_url( $pattern ) {
		$base_request = $this->request_base_url( );
		$full_request = $base_request . $this->request_uri;
		$pattern      = \Supersoniq\substr_after( $pattern, '//' );
		$pattern      = \Supersoniq\must_not_ends_with( $pattern, '/' );
		// echo $base_request . ' &lt; ' . $pattern . ' &lt; ' . $full_request . HTML_EOL;
		if ( \Supersoniq\starts_with( $pattern, $base_request ) && \Supersoniq\starts_with( $full_request, $pattern ) ) {
			$this->request_base_uri .= \Supersoniq\substr_after( $pattern, $base_request );
			$this->request_uri = \Supersoniq\substr_after( $full_request, $pattern );
			return TRUE;
		}
		return FALSE;
	}
	private function is_pattern_match_by_uri( $base_uri ) {
		return $this->is_pattern_match_by_url( '//' . $this->request_base_url( $base_uri ) );
	}
	private function is_pattern_match_by_port( $pattern ) {
		$pattern  = \Supersoniq\substr_after( $pattern, ':' );
		$port     = \Supersoniq\substr_before( $pattern, '/' );
		$base_uri = \Supersoniq\substr_after( $pattern, '/' );
		return $this->is_pattern_match_by_url( '//' . $this->request_base_url( '/' . $base_uri, $port ) );
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
		if ( is_null( $base_uri ) ) {
			$base_uri = $this->request_base_uri;
		}
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

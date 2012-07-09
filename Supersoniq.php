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
	private $request_base_url;
	private $request_uri;


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {

		// Initialize root path
		define( 'SUPERSONIQ_ROOT_PATH', dirname( dirname( __FILE__ ) ) . '/' );

		// Original request 
		$this->request_base_url = $_SERVER[ 'SERVER_NAME' ];
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
		define( 'SUPERSONIQ_REQUEST_BASE_URL', $this->request_base_url );
		define( 'SUPERSONIQ_REQUEST_URI'     , $this->request_uri );

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
	private function is_pattern_match( $pattern ) {
		if ( \Supersoniq\starts_with( $pattern, array( 'http://', 'https://', '//' ) ) ) {
			return $this->is_pattern_match_by_url( $pattern );
		} else {
			return $this->is_pattern_match_by_uri( $pattern );
		}
	}
	private function is_pattern_match_by_url( $pattern ) {
		$request = $this->request_base_url . $this->request_uri;
		$condition = \Supersoniq\substr_after( $pattern, '//' );
		$condition = \Supersoniq\must_not_ends_with( $condition, '/' );
		// echo $request . '<>'. $condition . '<br>' . PHP_EOL;
		if ( \Supersoniq\starts_with( $request, $condition ) ) {
			$this->request_base_url = $condition;
			$this->request_uri = \Supersoniq\substr_after( $request, $condition );
			return TRUE;
		}
		return FALSE;
	}
	private function is_pattern_match_by_uri( $pattern ) {
		return $this->is_pattern_match_by_url( '//' . $this->request_base_url . $pattern );
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

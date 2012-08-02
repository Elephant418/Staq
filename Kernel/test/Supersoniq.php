<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Test;

require_once( dirname( __FILE__ ) . '/__Base.php' );

class Supersoniq extends __Base {


	/*************************************************************************
	  TESTS
	 *************************************************************************/
	public function test_default_route( ) {
		$service = $this->default_supersoniq_request( );
		return $this->assert_equals( $service->route, '/path' );
	}

	public function test_default_base_url( ) {
		$service = $this->default_supersoniq_request( );
		return $this->assert_equals( \Supersoniq::$BASE_URL, 'http://hostname' );
	}

	public function test_default_platform( ) {
		$service = $this->default_supersoniq_request( );
		return $this->assert_equals( \Supersoniq::$PLATFORM_NAME, 'prod' );
	}

	public function test_default_application( ) {
		$document_root = $_SERVER[ 'DOCUMENT_ROOT' ];
		$_SERVER[ 'DOCUMENT_ROOT' ] = dirname( __FILE__ );
		$service = $this->default_supersoniq_request( );
		$_SERVER[ 'DOCUMENT_ROOT' ] = $document_root;
		return $this->assert_equals( \Supersoniq::$APPLICATION_NAME, 'Supersoniq\Starter' );
	}

	public function test_default_application_guessed( ) {
		$document_root = $_SERVER[ 'DOCUMENT_ROOT' ];
		$_SERVER[ 'DOCUMENT_ROOT' ] = SUPERSONIQ_ROOT_PATH . 'Project_Example/Sub_Project/public';
		$service = $this->default_supersoniq_request( );
		$_SERVER[ 'DOCUMENT_ROOT' ] = $document_root;
		return $this->assert_equals( \Supersoniq::$APPLICATION_NAME, 'Project_Example\Sub_Project' );
	}

	public function test_base_url_path( ) {
		$service = $this->complex_supersoniq( 'http://localhost:5000/path/tralalala' );
		return $this->assert_equals( \Supersoniq::$BASE_URL, 'http://localhost:5000/path' );
	}

	public function test_base_url_double_path( ) {
		$service = $this->complex_supersoniq( 'http://localhost:5000/path/bou/tralalala' );
		return $this->assert_equals( \Supersoniq::$BASE_URL, 'http://localhost:5000/path/bou' );
	}

	public function test_route_path( ) {
		$service = $this->complex_supersoniq( 'http://localhost:5000/path/tralalala' );
		return $this->assert_equals( $service->route, '/tralalala' );
	}

	public function test_route_double_path( ) {
		$service = $this->complex_supersoniq( 'http://localhost:5000/path/bou/tralalala' );
		return $this->assert_equals( $service->route, '/tralalala' );
	}

	public function test_complex_platform_full( ) {
		$service = $this->complex_supersoniq( 'http://localhost:5000/path' );
		return $this->assert_equals( \Supersoniq::$PLATFORM_NAME, 'full' );
	}

	public function test_complex_platform_hostport( ) {
		$service = $this->complex_supersoniq( 'http://localhost:5000/tralalala' );
		return $this->assert_equals( \Supersoniq::$PLATFORM_NAME, 'hostport' );
	}

	public function test_complex_platform_host( ) {
		$service = $this->complex_supersoniq( 'http://localhost/great/power' );
		return $this->assert_equals( \Supersoniq::$PLATFORM_NAME, 'host' );
	}

	public function test_complex_platform_portpath( ) {
		$service = $this->complex_supersoniq( 'http://hostname:5000/path/tralalala' );
		return $this->assert_equals( \Supersoniq::$PLATFORM_NAME, 'portpath' );
	}

	public function test_complex_platform_port( ) {
		$service = $this->complex_supersoniq( 'http://hostname:5000/tralalala' );
		return $this->assert_equals( \Supersoniq::$PLATFORM_NAME, 'port' );
	}

	public function test_complex_platform_path( ) {
		$service = $this->complex_supersoniq( 'http://hostname/path/tralalala' );
		return $this->assert_equals( \Supersoniq::$PLATFORM_NAME, 'path' );
	}

	public function test_complex_platform_default( ) {
		$service = $this->complex_supersoniq( 'http://hostname/great/power' );
		return $this->assert_equals( \Supersoniq::$PLATFORM_NAME, 'prod' );
	}



	/*************************************************************************
	  UTILS
	 *************************************************************************/
	public function default_supersoniq_request( ) {
		try {
			$service = new \Supersoniq\Service;
			$service->start( 'http://hostname/path' );
		} catch ( \Exception $exception ) { }
		return $service;
	}
	public function complex_supersoniq( $request ) {
		$service = ( new \Supersoniq\Service )
			->application( 'coco', '/bou' )
			->platform( 'full'     , 'http://localhost:5000/path')
			->platform( 'hostport' , 'http://localhost:5000/')
			->platform( 'exception', 'http://localhost:5000/path/subpath')
			->platform( 'host'     , 'http://localhost/')
			->platform( 'portpath' , ':5000/path')
			->platform( 'port'     , ':5000')
			->platform( 'path'     , '/path');
		try {
			$service->start( $request );
		} catch ( \Exception $exception ) { }
		return $service;
	}

}





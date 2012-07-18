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
		$sq = $this->default_supersoniq_request( );
		return $this->assert_equals( $sq->route, '/path' );
	}

	public function test_default_base_url( ) {
		$sq = $this->default_supersoniq_request( );
		return $this->assert_equals( $sq->base_url, 'http://hostname' );
	}

	public function test_default_platform( ) {
		$sq = $this->default_supersoniq_request( );
		return $this->assert_equals( $sq->platform_name, 'prod' );
	}

	public function test_default_application( ) {
		$document_root = $_SERVER[ 'DOCUMENT_ROOT' ];
		$_SERVER[ 'DOCUMENT_ROOT' ] = dirname( __FILE__ );
		$sq = $this->default_supersoniq_request( );
		$_SERVER[ 'DOCUMENT_ROOT' ] = $document_root;
		return $this->assert_equals( $sq->application_path, 'Supersoniq/Starter' );
	}

	public function test_default_application_guessed( ) {
		$document_root = $_SERVER[ 'DOCUMENT_ROOT' ];
		$_SERVER[ 'DOCUMENT_ROOT' ] = SUPERSONIQ_ROOT_PATH . 'Project_Example/Sub_Project/public';
		$sq = $this->default_supersoniq_request( );
		$_SERVER[ 'DOCUMENT_ROOT' ] = $document_root;
		return $this->assert_equals( $sq->application_path, 'Project_Example/Sub_Project' );
	}

	public function test_base_url_path( ) {
		$sq = $this->complex_supersoniq( );
		$sq->run( 'http://localhost:5000/path/tralalala' );
		return $this->assert_equals( $sq->base_url, 'http://localhost:5000/path' );
	}

	public function test_base_url_double_path( ) {
		$sq = $this->complex_supersoniq( );
		$sq->run( 'http://localhost:5000/path/bou/tralalala' );
		return $this->assert_equals( $sq->base_url, 'http://localhost:5000/path/bou' );
	}

	public function test_complex_platform_full( ) {
		$sq = $this->complex_supersoniq( );
		$sq->run( 'http://localhost:5000/path' );
		return $this->assert_equals( $sq->platform_name, 'full' );
	}

	public function test_complex_platform_hostport( ) {
		$sq = $this->complex_supersoniq( );
		$sq->run( 'http://localhost:5000/tralalala' );
		return $this->assert_equals( $sq->platform_name, 'hostport' );
	}

	public function test_complex_platform_host( ) {
		$sq = $this->complex_supersoniq( );
		$sq->run( 'http://localhost/great/power' );
		return $this->assert_equals( $sq->platform_name, 'host' );
	}

	public function test_complex_platform_portpath( ) {
		$sq = $this->complex_supersoniq( );
		$sq->run( 'http://hostname:5000/path/tralalala' );
		return $this->assert_equals( $sq->platform_name, 'portpath' );
	}

	public function test_complex_platform_port( ) {
		$sq = $this->complex_supersoniq( );
		$sq->run( 'http://hostname:5000/tralalala' );
		return $this->assert_equals( $sq->platform_name, 'port' );
	}

	public function test_complex_platform_path( ) {
		$sq = $this->complex_supersoniq( );
		$sq->run( 'http://hostname/path/tralalala' );
		return $this->assert_equals( $sq->platform_name, 'path' );
	}

	public function test_complex_platform_default( ) {
		$sq = $this->complex_supersoniq( );
		$sq->run( 'http://hostname/great/power' );
		return $this->assert_equals( $sq->platform_name, 'prod' );
	}



	/*************************************************************************
	  UTILS
	 *************************************************************************/
	public function default_supersoniq_request( ) {
		$sq = new \Supersoniq;
		$sq->run( 'http://hostname/path' );
		return $sq;
	}
	public function complex_supersoniq( ) {
		$sq = ( new \Supersoniq )
			->application( 'coco', '/bou' )
			->platform( 'full'     , 'http://localhost:5000/path')
			->platform( 'hostport' , 'http://localhost:5000/')
			->platform( 'exception', 'http://localhost:5000/path/subpath')
			->platform( 'host'     , 'http://localhost/')
			->platform( 'portpath' , ':5000/path')
			->platform( 'port'     , ':5000')
			->platform( 'path'     , '/path');
		return $sq;
	}

}





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
		$sq = $this->_default_supersoniq_request( );
		return $this->_assert_equals( $sq->route, '/path' );
	}
	public function test_default_platform( ) {
		$sq = $this->_default_supersoniq_request( );
		return $this->_assert_equals( $sq->platform_name, 'prod' );
	}
	public function test_default_application( ) {
		$document_root = $_SERVER[ 'DOCUMENT_ROOT' ];
		$_SERVER[ 'DOCUMENT_ROOT' ] = dirname( __FILE__ );
		$sq = $this->_default_supersoniq_request( );
		$_SERVER[ 'DOCUMENT_ROOT' ] = $document_root;
		return $this->_assert_equals( $sq->application_path, 'Supersoniq/Starter' );
	}
	public function test_default_application_guessed( ) {
		$document_root = $_SERVER[ 'DOCUMENT_ROOT' ];
		$_SERVER[ 'DOCUMENT_ROOT' ] = SUPERSONIQ_ROOT_PATH . 'Project_Example/Sub_Project/public';
		$sq = $this->_default_supersoniq_request( );
		$_SERVER[ 'DOCUMENT_ROOT' ] = $document_root;
		return $this->_assert_equals( $sq->application_path, 'Project_Example/Sub_Project' );
	}


	/*************************************************************************
	  UTILS
	 *************************************************************************/
	public function _default_supersoniq_request( ) {
		$sq = new \Supersoniq( );
		$sq->run( 'http://hostname/path' );
		return $sq;
	}

}





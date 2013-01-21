<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class DataTest extends StaqTestCase {




	/*************************************************************************
	  ATTRIBUTES
	 *************************************************************************/
	public $connection;




	/*************************************************************************
	  DATABASE METHODS			 
	 *************************************************************************/
	protected function setUp( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		( new \Stack\Database\Request )->load_mysql_file( $app->get_path( 'dataset/user.sql' ) );
	}




	/*************************************************************************
	  TEST METHODS			 
	 *************************************************************************/
	public function test_select_user__no_match( ) {
		$user = ( new \Stack\Model\User )->by_id( 1664 );
		$this->assertFalse( $user->exists( ) );
	}

	public function test_select_user__match( ) {
		$user = ( new \Stack\Model\User )->by_id( 1 );
		$this->assertTrue( $user->exists( ) );
		$this->assertEquals( 'Thomas', $user[ 'name' ] );
	}
}
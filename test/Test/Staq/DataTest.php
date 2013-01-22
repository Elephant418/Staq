<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class DataTest extends StaqTestCase {




	/*************************************************************************
	  ATTRIBUTES
	 *************************************************************************/
	public $connection;




	/*************************************************************************
	  GLOBAL METHODS			 
	 *************************************************************************/
	protected function setUp( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		( new \Stack\Database\Request )
			->require_database( )
			->load_mysql_file( $app->get_path( 'dataset/user.sql' ) );
	}

	protected function tearDown( ) {
		( new \Stack\Database\Request )->load_mysql_file( \Staq\Application::get_path( 'dataset/reset.sql' ) );
	}




	/*************************************************************************
	  TEST METHODS			 
	 *************************************************************************/
	public function test_select__no_match( ) {
		$user = ( new \Stack\Model\User )->by_id( 1664 );
		$this->assertFalse( $user->exists( ) );
	}

	public function test_select__match( ) {
		$user = ( new \Stack\Model\User )->by_id( 1 );
		$this->assertTrue( $user->exists( ) );
		$this->assertEquals( 'Thomas', $user[ 'name' ] );
	}

	public function test_select__all( ) {
		$users = ( new \Stack\Model\User )->all( );
		$this->assertEquals( 4, count( $users ) );
		$names = [ ];
		foreach ( $users as $user ) {
			$names[ $user->id ] = $user[ 'name' ];
		}
		$this->assertContains( 'Thomas' , $names );
		$this->assertContains( 'Romaric', $names );
		$this->assertContains( 'Simon'  , $names );
		$this->assertContains( 'Sylvain', $names );
	}

	public function test_insert__exception( ) {
		$this->setExpectedException( 'PDOException' );
		$user = new \Stack\Model\User;
		$this->assertFalse( $user->exists( ) );
		$user->save( );
	}

	public function test_insert__valid( ) {
		$user = new \Stack\Model\User;
		$this->assertFalse( $user->exists( ) );
		$user[ 'name' ] = 'Christophe';
		$user->save( );
		$this->assertTrue( $user->exists( ) );
		$id = $user->id;
		unset( $user );
		$user = ( new \Stack\Model\User )->by_id( $id );
		$this->assertTrue( $user->exists( ) );
		$this->assertEquals( 'Christophe', $user[ 'name' ] );
	}

	public function test_update( ) {
		$user = ( new \Stack\Model\User )->by_id( 1 );
		$this->assertEquals( 'Thomas', $user[ 'name' ] );
		$user[ 'name' ] = 'Antoine';
		$user->save( );
		unset( $user );
		$user = ( new \Stack\Model\User )->by_id( 1 );
		$this->assertEquals( 'Antoine', $user[ 'name' ] );
	}

	public function test_delete( ) {
		$user = ( new \Stack\Model\User )->by_id( 1 );
		$user->delete( );
		$this->assertFalse( $user->exists( ) );
		unset( $user );
		$user = ( new \Stack\Model\User )->by_id( 1 );
		$this->assertFalse( $user->exists( ) );
	}
}
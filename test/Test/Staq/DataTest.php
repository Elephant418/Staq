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
		$app = \Staq\App::create( $this->project_namespace )
			->set_platform( 'local' );
		( new \Stack\Database\Request )
			->require_database( )
			->load_mysql_file( $app->get_path( 'dataset/set.sql' ) );
	}

	protected function tearDown( ) {
		( new \Stack\Database\Request )->load_mysql_file( \Staq\App::get_path( 'dataset/reset.sql' ) );
	}




	/*************************************************************************
	  VARCHAR SCUD TEST METHODS			 
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

	public function test_select__list_all( ) {
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

	public function test_select__list_by_ids( ) {
		$users = ( new \Stack\Model\User )->fetch( [ 'id' => [ 1, 2 ] ] );
		$this->assertEquals( 2, count( $users ) );
		$names = [ ];
		foreach ( $users as $user ) {
			$names[ $user->id ] = $user[ 'name' ];
		}
		$this->assertContains( 'Thomas' , $names );
		$this->assertContains( 'Romaric', $names );
	}

	public function test_insert__exception( ) {
		$user = new \Stack\Model\User;
		$this->assertFalse( $user->exists( ) );
		$this->setExpectedException( 'PDOException' );
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




	/*************************************************************************
	  MANY TO ONE RELATION SCUD TEST METHODS			 
	 *************************************************************************/
	public function test_select_relation__no_match( ) {
		$article = ( new \Stack\Model\Article )->by_id( 3 );
		$this->assertTrue( $article->exists( ) );
		$this->assertEquals( 'Dataq', $article[ 'title' ] );
		$this->assertNull( $article[ 'author' ] );
	}

	public function test_select_relation__match( ) {
		$article = ( new \Stack\Model\Article )->by_id( 1 );
		$this->assertTrue( $article->exists( ) );
		$this->assertEquals( 'Staq', $article[ 'title' ] );
		$this->assertEquals( 'Stack\\Model\\User', get_class( $article[ 'author' ] ) );
		$this->assertEquals( 'Thomas', $article[ 'author' ][ 'name' ] );
	}

	public function test_update_relation__exception( ) {
		$article = ( new \Stack\Model\Article )->by_id( 3 );
		$this->setExpectedException( 'Stack\\Exception\\NotRightInput' );
		$article[ 'author' ] = 2;
	}

	public function test_update_relation__valid( ) {
		$article = ( new \Stack\Model\Article )->by_id( 3 );
		$user    = ( new \Stack\Model\User    )->by_id( 2 );
		$article[ 'author' ] = $user;
		$article->save( );
		unset( $article, $user );
		$article = ( new \Stack\Model\Article )->by_id( 3 );
		$user = $article[ 'author' ];
		$this->assertTrue( $user->exists( ) );
		$this->assertEquals( 'Romaric', $user[ 'name' ] );
	}
}
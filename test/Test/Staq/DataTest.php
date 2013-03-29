<?php

namespace Test\Staq;

$autoload = '/../../../vendor/autoload.php';
if ( is_file( __DIR__ . $autoload ) ) {
    require_once( __DIR__ . $autoload );
} else {
    require_once( __DIR__ . '/../../../' . $autoload );
}

class DataTest extends StaqTestCase {




	/*************************************************************************
	  ATTRIBUTES
	 *************************************************************************/
	public $connection;




	/*************************************************************************
	  GLOBAL METHODS			 
	 *************************************************************************/
	protected function setUp( ) {
		$app = \Staq\App::create( $this->projectNamespace )
			->setPlatform( 'local' );
		( new \Stack\Database\Request )
			->requireDatabase( )
			->loadMysqlFile( $app->getPath( 'dataset/set.sql' ) );
	}

	protected function tearDown( ) {
		( new \Stack\Database\Request )->loadMysqlFile( \Staq::App()->getPath( 'dataset/reset.sql' ) );
	}




	/*************************************************************************
	  VARCHAR SCUD TEST METHODS			 
	 *************************************************************************/
	public function test_select__no_match( ) {
		$user = ( new \Stack\Entity\User )->fetchById( 1664 );
		$this->assertFalse( $user->exists( ) );
	}

	public function test_select__match( ) {
		$user = ( new \Stack\Entity\User )->fetchById( 1 );
		$this->assertTrue( $user->exists( ) );
		$this->assertEquals( 'Thomas', $user[ 'name' ] );
	}

	public function test_select__list_all( ) {
		$users = ( new \Stack\Entity\User )->fetchAll( );
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
		$users = ( new \Stack\Entity\User )->fetch( [ 'id' => [ 1, 2 ] ] );
		$this->assertEquals( 2, count( $users ) );
		$names = [ ];
		foreach ( $users as $user ) {
			$names[ $user->id ] = $user[ 'name' ];
		}
		$this->assertContains( 'Thomas' , $names );
		$this->assertContains( 'Romaric', $names );
	}

	public function test_select__list_by_ids_and_limit( ) {
		$users = ( new \Stack\Entity\User )->fetch( [ 'id' => [ 1, 2 ] ], 1 );
		$this->assertEquals( 1, count( $users ) );
		$this->assertEquals( 'Thomas' , $users[ 0 ][ 'name' ] );
	}

	public function test_select__list_by_ids_and_limit_and_order( ) {
		$users = ( new \Stack\Entity\User )->fetch( [ 'id' => [ 1, 2 ] ], 1, 'id DESC' );
		$this->assertEquals( 1, count( $users ) );
		$this->assertEquals( 'Romaric' , $users[ 0 ][ 'name' ] );
	}

	public function test_select__list_with_like_statement( ) {
		$users = ( new \Stack\Entity\User )->fetch( [ [ 'name', 'LIKE', 'S%' ] ] );
		$this->assertEquals( 2, count( $users ) );
		$names = [ ];
		foreach ( $users as $user ) {
			$names[ $user->id ] = $user[ 'name' ];
		}
		$this->assertContains( 'Simon' , $names );
		$this->assertContains( 'Sylvain', $names );
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
		$user = ( new \Stack\Entity\User )->fetchById( $id );
		$this->assertTrue( $user->exists( ) );
		$this->assertEquals( 'Christophe', $user[ 'name' ] );
	}

	public function test_update( ) {
		$user = ( new \Stack\Entity\User )->fetchById( 1 );
		$this->assertEquals( 'Thomas', $user[ 'name' ] );
		$user[ 'name' ] = 'Antoine';
		$user->save( );
		unset( $user );
		$user = ( new \Stack\Entity\User )->fetchById( 1 );
		$this->assertEquals( 'Antoine', $user[ 'name' ] );
	}

	public function test_delete( ) {
		$user = ( new \Stack\Entity\User )->fetchById( 1 );
		$user->delete( );
		$this->assertFalse( $user->exists( ) );
		unset( $user );
		$user = ( new \Stack\Entity\User )->fetchById( 1 );
		$this->assertFalse( $user->exists( ) );
	}




	/*************************************************************************
	  MANY TO ONE RELATION SCUD TEST METHODS			 
	 *************************************************************************/
	public function test_select_relation__many_to_one__no_match( ) {
		$article = ( new \Stack\Entity\Article )->fetchById( 3 );
		$this->assertTrue( $article->exists( ) );
		$this->assertEquals( 'Dataq', $article[ 'title' ] );
		$this->assertNull( $article[ 'author' ] );
	}

	public function test_select_relation__many_to_one__match( ) {
		$article = ( new \Stack\Entity\Article )->fetchById( 1 );
		$this->assertTrue( $article->exists( ) );
		$this->assertEquals( 'Staq', $article[ 'title' ] );
		$this->assertEquals( 'Stack\\Model\\User', get_class( $article[ 'author' ] ) );
		$this->assertEquals( 'Thomas', $article[ 'author' ][ 'name' ] );
	}

	public function test_update_relation__many_to_one__valid( ) {
		$article = ( new \Stack\Entity\Article )->fetchById( 3 );
		$user    = ( new \Stack\Entity\User )->fetchById( 2 );
		$article[ 'author' ] = $user;
		$article->save( );
		unset( $article, $user );
		$article = ( new \Stack\Entity\Article )->fetchById( 3 );
		$user = $article[ 'author' ];
		$this->assertTrue( $user->exists( ) );
		$this->assertEquals( 'Romaric', $user[ 'name' ] );
	}




	/*************************************************************************
	  ONE TO MANY RELATION SCUD TEST METHODS			 
	 *************************************************************************/
	public function test_select_relation__one_to_many__no_match( ) {
		$user = ( new \Stack\Entity\User )->fetchById( 2 );
		$this->assertTrue( $user->exists( ) );
		$this->assertEquals( 'Romaric', $user[ 'name' ] );
		$this->assertEquals( [ ], $user[ 'articles' ] );
	}

	public function test_select_relation__one_to_many__match( ) {
		$user = ( new \Stack\Entity\User )->fetchById( 1 );
		$this->assertTrue( $user->exists( ) );
		$this->assertEquals( 'Thomas', $user[ 'name' ] );
		$this->assertEquals( 2, count( $user[ 'articles' ] ) );
	}
}
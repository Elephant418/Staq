<?php

namespace Test\Staq;

$autoload = '/../../../vendor/autoload.php';
if ( is_file( __DIR__ . $autoload ) ) {
    require_once( __DIR__ . $autoload );
} else {
    require_once( __DIR__ . '/../../../' . $autoload );
}

class ServerTest extends WebTestCase {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $starterNamespaces = [ 'Staq\\App\\Starter', 'Staq\\Core\\View', 'Staq\\Core\\Router', 'Staq\\Core\\Ground', 'Pixel418\\Iniliq' ];




	/*************************************************************************
	  UTIL METHODS             
	 *************************************************************************/
	public function appendProjectNamespace( $name ) {
		$names = array_map( [ $this, 'getProjectClass' ], func_get_args( ) );
		return array_merge( $names, $this->starterNamespaces );
	}



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( ) {
		$this->projectNamespace .= 'Application';
	}




	/*************************************************************************
	  GLOBAL METHODS			 
	 *************************************************************************/
	protected function setUp( ) {
		parent::setUp( );
		$this->getRequestUrl( 'http://localhost/' );
	}




	/*************************************************************************
	  SIMPLE PLATFORM & SIMPLE APPLICATION TEST METHODS             
	 *************************************************************************/
	public function test_empty_project__extensions( ) {
		$app = ( new \Staq\Server )
			->addPlatform( 'local' )
			->launch( );
		$this->assertEquals( $this->starterNamespaces, $app->getExtensionNamespaces( ) );
	}

	public function test_empty_project__platform__default( ) {
		$app = ( new \Staq\Server )
			->launch( );
		$this->assertEquals( 'prod', $app->getPlatform( ) );
	}

	public function test_empty_project__platform__setted( ) {
		$app = ( new \Staq\Server )
			->addPlatform( 'local' )
			->launch( );
		$this->assertEquals( 'local', $app->getPlatform( ) );
	}

	public function test_no_configuration__extensions( ) {
		$projectNamespace = $this->getProjectClass( 'NoConfiguration' );
		$app = ( new \Staq\Server )
			->addApplication( $projectNamespace, '/' )
			->addPlatform( 'local' )
			->launch( );
		$expected = $this->appendProjectNamespace( 'NoConfiguration' );
		$this->assertEquals( $expected, $app->getExtensionNamespaces( ) );
	}

	public function test_simple_configuration__extensions( ) {
		$projectNamespace = $this->getProjectClass( 'SimpleConfiguration' );
		$app = ( new \Staq\Server )
			->addApplication( $projectNamespace, '/' )
			->addPlatform( 'local' )
			->launch( );
		$expected = $this->appendProjectNamespace( 'SimpleConfiguration' );
		$this->assertEquals( $expected, $app->getExtensionNamespaces( ) );
	}

	public function test_extend_no_configuration__extensions( ) {
		$projectNamespace = $this->getProjectClass( 'ExtendNoConfiguration' );
		$app = ( new \Staq\Server )
			->addApplication( $projectNamespace, '/' )
			->addPlatform( 'local' )
			->launch( );
		$expected = $this->appendProjectNamespace( 'ExtendNoConfiguration', 'NoConfiguration' );
		$this->assertEquals( $expected, $app->getExtensionNamespaces( ) );
	}

	public function test_without_starter__extensions( ) {
		$projectNamespace = $this->getProjectClass( 'WithoutStarter' );
		$app = ( new \Staq\Server )
			->addApplication( $projectNamespace, '/' )
			->addPlatform( 'local' )
			->launch( );
		$expected = $this->appendProjectNamespace( 'WithoutStarter' );
		\UArray::doRemoveValue( $expected, 'Staq\\App\\Starter' );
		$this->assertEquals( $expected, $app->getExtensionNamespaces( ) );
	}




	/*************************************************************************
	  PLATFORM SWITCHER TEST METHODS             
	 *************************************************************************/
	public function test_platform_switcher__default( ) {
		$this->getRequestUrl( 'http://localhost/' );
		$app = ( new \Staq\Server )
			->addPlatform( 'local', '/local')
			->addPlatform( 'remote', '//example.com')
			->addPlatform( 'debug', ':8020')
			->launch( );
		$this->assertEquals( 'prod', $app->getPlatform( ) );
	}

	public function test_platform_switcher__path( ) {
		$this->getRequestUrl( 'http://localhost/local/bou' );
		$app = ( new \Staq\Server )
			->addPlatform( 'local', '/local')
			->addPlatform( 'remote', '//example.com')
			->addPlatform( 'debug', ':8020')
			->launch( );
		$this->assertEquals( 'local' , $app->getPlatform( ) );
		$this->assertEquals( '/local', $app->getBaseUri( ) );
		$this->assertEquals( '/bou'  , $app->getCurrentUri( ) );
	}

	public function test_platform_switcher__domain( ) {
		$this->getRequestUrl( 'http://example.com/lievre/tortue' );
		$app = ( new \Staq\Server )
			->addPlatform( 'local', '/local')
			->addPlatform( 'remote', '//example.com')
			->addPlatform( 'debug', ':8020')
			->launch( );
		$this->assertEquals( 'remote' , $app->getPlatform( ) );
		$this->assertEquals( '', $app->getBaseUri( ) );
		$this->assertEquals( '/lievre/tortue'  , $app->getCurrentUri( ) );
	}

	public function test_platform_switcher__port( ) {
		$this->getRequestUrl( 'http://localhost:8020/lievre/tortue' );
		$app = ( new \Staq\Server )
			->addPlatform( 'local', '/local')
			->addPlatform( 'remote', '//example.com')
			->addPlatform( 'debug', ':8020')
			->launch( );
		$this->assertEquals( 'debug' , $app->getPlatform( ) );
		$this->assertEquals( '', $app->getBaseUri( ) );
		$this->assertEquals( '/lievre/tortue'  , $app->getCurrentUri( ) );
	}




	/*************************************************************************
	  APPLICATION SWITCHER TEST METHODS             
	 *************************************************************************/
	public function test_application_switcher__default( ) {
		$this->getRequestUrl( 'http://localhost/' );
		$app = ( new \Staq\Server )
			->addApplication( $this->getProjectClass( 'NoConfiguration' ), '/noconf' )
			->addApplication( $this->getProjectClass( 'SimpleConfiguration' ), '//example.com')
			->addApplication( $this->getProjectClass( 'WithoutStarter' ), ':8020')
			->launch( );
		$this->assertEquals( 'Staq\\App\\Starter', $app->getNamespace( ) );
	}

	public function test_application_switcher__path( ) {
		$this->getRequestUrl( 'http://localhost/noconf/bou' );
		$app = ( new \Staq\Server )
			->addApplication( $this->getProjectClass( 'NoConfiguration' ), '/noconf' )
			->addApplication( $this->getProjectClass( 'SimpleConfiguration' ), '//example.com')
			->addApplication( $this->getProjectClass( 'WithoutStarter' ), ':8020')
			->launch( );
		$this->assertEquals( $this->getProjectClass( 'NoConfiguration' ), $app->getNamespace( ) );
		$this->assertEquals( '/noconf', $app->getBaseUri( ) );
		$this->assertEquals( '/bou'  , $app->getCurrentUri( ) );
	}

	public function test_application_switcher__domain( ) {
		$this->getRequestUrl( 'http://example.com/lievre/tortue' );
		$app = ( new \Staq\Server )
			->addApplication( $this->getProjectClass( 'NoConfiguration' ), '/noconf' )
			->addApplication( $this->getProjectClass( 'SimpleConfiguration' ), '//example.com')
			->addApplication( $this->getProjectClass( 'WithoutStarter' ), ':8020')
			->launch( );
		$this->assertEquals( $this->getProjectClass( 'SimpleConfiguration' ), $app->getNamespace( ) );
		$this->assertEquals( '', $app->getBaseUri( ) );
		$this->assertEquals( '/lievre/tortue'  , $app->getCurrentUri( ) );
	}

	public function test_application_switcher__port( ) {
		$this->getRequestUrl( 'http://localhost:8020/lievre/tortue' );
		$app = ( new \Staq\Server )
			->addApplication( $this->getProjectClass( 'NoConfiguration' ), '/noconf' )
			->addApplication( $this->getProjectClass( 'SimpleConfiguration' ), '//example.com')
			->addApplication( $this->getProjectClass( 'WithoutStarter' ), ':8020')
			->launch( );
		$this->assertEquals( $this->getProjectClass( 'WithoutStarter' ), $app->getNamespace( ) );
		$this->assertEquals( '', $app->getBaseUri( ) );
		$this->assertEquals( '/lievre/tortue'  , $app->getCurrentUri( ) );
	}




	/*************************************************************************
	  APPLICATION & PLATFORM SWITCHER TEST METHODS             
	 *************************************************************************/
	public function test_application_n_platform_switcher__default( ) {
		$this->getRequestUrl( 'http://localhost/bou' );
		$app = ( new \Staq\Server )
			->addApplication( $this->getProjectClass( 'NoConfiguration' ), '/noconf' )
			->addPlatform( 'local', '/local')
			->launch( );
		$this->assertEquals( 'Staq\\App\\Starter', $app->getNamespace( ) );
		$this->assertEquals( 'prod', $app->getPlatform( ) );
		$this->assertEquals( '/bou', $app->getCurrentUri( ) );
	}

	public function test_application_n_platform_switcher__match_application( ) {
		$this->getRequestUrl( 'http://localhost/noconf/bou' );
		$app = ( new \Staq\Server )
			->addApplication( $this->getProjectClass( 'NoConfiguration' ), '/noconf' )
			->addPlatform( 'local', '/local')
			->launch( );
		$this->assertEquals( $this->getProjectClass( 'NoConfiguration' ), $app->getNamespace( ) );
		$this->assertEquals( 'prod', $app->getPlatform( ) );
		$this->assertEquals( '/bou', $app->getCurrentUri( ) );
	}

	public function test_application_n_platform_switcher__match_platform( ) {
		$this->getRequestUrl( 'http://localhost/local/bou' );
		$app = ( new \Staq\Server )
			->addApplication( $this->getProjectClass( 'NoConfiguration' ), '/noconf' )
			->addPlatform( 'local', '/local')
			->launch( );
		$this->assertEquals( 'Staq\\App\\Starter', $app->getNamespace( ) );
		$this->assertEquals( 'local', $app->getPlatform( ) );
		$this->assertEquals( '/bou' , $app->getCurrentUri( ) );
	}

	public function test_application_n_platform_switcher__match_application_n_platform( ) {
		$this->getRequestUrl( 'http://localhost/local/noconf/bou' );
		$app = ( new \Staq\Server )
			->addApplication( $this->getProjectClass( 'NoConfiguration' ), '/noconf' )
			->addPlatform( 'local', '/local')
			->launch( );
		$this->assertEquals( $this->getProjectClass( 'NoConfiguration' ), $app->getNamespace( ) );
		$this->assertEquals( 'local', $app->getPlatform( ) );
		$this->assertEquals( '/bou' , $app->getCurrentUri( ) );
	}
}
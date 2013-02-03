<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class ServerTest extends WebTestCase {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $starter_namespaces = [ 'Staq\App\Starter', 'Staq\Core\View', 'Staq\Core\Router', 'Staq\Core\Ground' ];




	/*************************************************************************
	  UTIL METHODS             
	 *************************************************************************/
	public function append_project_namespace( $name ) {
		$names = array_map( [ $this, 'get_project_class' ], func_get_args( ) );
		return array_merge( $names, $this->starter_namespaces );
	}



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( ) {
		$this->project_namespace .= 'Application';
	}




	/*************************************************************************
	  GLOBAL METHODS			 
	 *************************************************************************/
	protected function setUp( ) {
		parent::setUp( );
		$this->get_request_url( 'http://localhost/' );
	}




	/*************************************************************************
	  SIMPLE PLATFORM & SIMPLE APPLICATION TEST METHODS             
	 *************************************************************************/
	public function test_empty_project__extensions( ) {
		$app = ( new \Staq\Server )
			->add_platform( 'local' )
			->launch( );
		$this->assertEquals( $this->starter_namespaces, $app->get_extension_namespaces( ) );
	}

	public function test_empty_project__platform__default( ) {
		$app = ( new \Staq\Server )
			->launch( );
		$this->assertEquals( 'prod', $app->get_platform( ) );
	}

	public function test_empty_project__platform__setted( ) {
		$app = ( new \Staq\Server )
			->add_platform( 'local' )
			->launch( );
		$this->assertEquals( 'local', $app->get_platform( ) );
	}

	public function test_no_configuration__extensions( ) {
		$project_namespace = $this->get_project_class( 'NoConfiguration' );
		$app = ( new \Staq\Server )
			->add_application( $project_namespace, '/' )
			->add_platform( 'local' )
			->launch( );
		$expected = $this->append_project_namespace( 'NoConfiguration' );
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}

	public function test_simple_configuration__extensions( ) {
		$project_namespace = $this->get_project_class( 'SimpleConfiguration' );
		$app = ( new \Staq\Server )
			->add_application( $project_namespace, '/' )
			->add_platform( 'local' )
			->launch( );
		$expected = $this->append_project_namespace( 'SimpleConfiguration' );
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}

	public function test_extend_no_configuration__extensions( ) {
		$project_namespace = $this->get_project_class( 'ExtendNoConfiguration' );
		$app = ( new \Staq\Server )
			->add_application( $project_namespace, '/' )
			->add_platform( 'local' )
			->launch( );
		$expected = $this->append_project_namespace( 'ExtendNoConfiguration', 'NoConfiguration' );
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}

	public function test_without_starter__extensions( ) {
		$project_namespace = $this->get_project_class( 'WithoutStarter' );
		$app = ( new \Staq\Server )
			->add_application( $project_namespace, '/' )
			->add_platform( 'local' )
			->launch( );
		$expected = [ $project_namespace, 'Staq\Core\Ground' ];
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}




	/*************************************************************************
	  PLATFORM SWITCHER TEST METHODS             
	 *************************************************************************/
	public function test_platform_switcher__default( ) {
		$this->get_request_url( 'http://localhost/' );
		$app = ( new \Staq\Server )
			->add_platform( 'local', '/local')
			->add_platform( 'remote', '//example.com')
			->add_platform( 'debug', ':8020')
			->launch( );
		$this->assertEquals( 'prod', $app->get_platform( ) );
	}

	public function test_platform_switcher__path( ) {
		$this->get_request_url( 'http://localhost/local/bou' );
		$app = ( new \Staq\Server )
			->add_platform( 'local', '/local')
			->add_platform( 'remote', '//example.com')
			->add_platform( 'debug', ':8020')
			->launch( );
		$this->assertEquals( 'local' , $app->get_platform( ) );
		$this->assertEquals( '/local', $app->get_base_uri( ) );
		$this->assertEquals( '/bou'  , $app->get_current_uri( ) );
	}

	public function test_platform_switcher__domain( ) {
		$this->get_request_url( 'http://example.com/lievre/tortue' );
		$app = ( new \Staq\Server )
			->add_platform( 'local', '/local')
			->add_platform( 'remote', '//example.com')
			->add_platform( 'debug', ':8020')
			->launch( );
		$this->assertEquals( 'remote' , $app->get_platform( ) );
		$this->assertEquals( '/', $app->get_base_uri( ) );
		$this->assertEquals( '/lievre/tortue'  , $app->get_current_uri( ) );
	}

	public function test_platform_switcher__port( ) {
		$this->get_request_url( 'http://localhost:8020/lievre/tortue' );
		$app = ( new \Staq\Server )
			->add_platform( 'local', '/local')
			->add_platform( 'remote', '//example.com')
			->add_platform( 'debug', ':8020')
			->launch( );
		$this->assertEquals( 'debug' , $app->get_platform( ) );
		$this->assertEquals( '/', $app->get_base_uri( ) );
		$this->assertEquals( '/lievre/tortue'  , $app->get_current_uri( ) );
	}




	/*************************************************************************
	  APPLICATION SWITCHER TEST METHODS             
	 *************************************************************************/
	public function test_application_switcher__default( ) {
		$this->get_request_url( 'http://localhost/' );
		$app = ( new \Staq\Server )
			->add_application( $this->get_project_class( 'NoConfiguration' ), '/noconf' )
			->add_application( $this->get_project_class( 'SimpleConfiguration' ), '//example.com')
			->add_application( $this->get_project_class( 'WithoutStarter' ), ':8020')
			->launch( );
		$this->assertEquals( 'Staq\\App\\Starter', $app->get_namespace( ) );
	}

	public function test_application_switcher__path( ) {
		$this->get_request_url( 'http://localhost/noconf/bou' );
		$app = ( new \Staq\Server )
			->add_application( $this->get_project_class( 'NoConfiguration' ), '/noconf' )
			->add_application( $this->get_project_class( 'SimpleConfiguration' ), '//example.com')
			->add_application( $this->get_project_class( 'WithoutStarter' ), ':8020')
			->launch( );
		$this->assertEquals( $this->get_project_class( 'NoConfiguration' ), $app->get_namespace( ) );
		$this->assertEquals( '/noconf', $app->get_base_uri( ) );
		$this->assertEquals( '/bou'  , $app->get_current_uri( ) );
	}

	public function test_application_switcher__domain( ) {
		$this->get_request_url( 'http://example.com/lievre/tortue' );
		$app = ( new \Staq\Server )
			->add_application( $this->get_project_class( 'NoConfiguration' ), '/noconf' )
			->add_application( $this->get_project_class( 'SimpleConfiguration' ), '//example.com')
			->add_application( $this->get_project_class( 'WithoutStarter' ), ':8020')
			->launch( );
		$this->assertEquals( $this->get_project_class( 'SimpleConfiguration' ), $app->get_namespace( ) );
		$this->assertEquals( '/', $app->get_base_uri( ) );
		$this->assertEquals( '/lievre/tortue'  , $app->get_current_uri( ) );
	}

	public function test_application_switcher__port( ) {
		$this->get_request_url( 'http://localhost:8020/lievre/tortue' );
		$app = ( new \Staq\Server )
			->add_application( $this->get_project_class( 'NoConfiguration' ), '/noconf' )
			->add_application( $this->get_project_class( 'SimpleConfiguration' ), '//example.com')
			->add_application( $this->get_project_class( 'WithoutStarter' ), ':8020')
			->launch( );
		$this->assertEquals( $this->get_project_class( 'WithoutStarter' ), $app->get_namespace( ) );
		$this->assertEquals( '/', $app->get_base_uri( ) );
		$this->assertEquals( '/lievre/tortue'  , $app->get_current_uri( ) );
	}




	/*************************************************************************
	  APPLICATION & PLATFORM SWITCHER TEST METHODS             
	 *************************************************************************/
	public function test_application_n_platform_switcher__default( ) {
		$this->get_request_url( 'http://localhost/bou' );
		$app = ( new \Staq\Server )
			->add_application( $this->get_project_class( 'NoConfiguration' ), '/noconf' )
			->add_platform( 'local', '/local')
			->launch( );
		$this->assertEquals( 'Staq\\App\\Starter', $app->get_namespace( ) );
		$this->assertEquals( 'prod', $app->get_platform( ) );
		$this->assertEquals( '/bou', $app->get_current_uri( ) );
	}

	public function test_application_n_platform_switcher__match_application( ) {
		$this->get_request_url( 'http://localhost/noconf/bou' );
		$app = ( new \Staq\Server )
			->add_application( $this->get_project_class( 'NoConfiguration' ), '/noconf' )
			->add_platform( 'local', '/local')
			->launch( );
		$this->assertEquals( $this->get_project_class( 'NoConfiguration' ), $app->get_namespace( ) );
		$this->assertEquals( 'prod', $app->get_platform( ) );
		$this->assertEquals( '/bou', $app->get_current_uri( ) );
	}

	public function test_application_n_platform_switcher__match_platform( ) {
		$this->get_request_url( 'http://localhost/local/bou' );
		$app = ( new \Staq\Server )
			->add_application( $this->get_project_class( 'NoConfiguration' ), '/noconf' )
			->add_platform( 'local', '/local')
			->launch( );
		$this->assertEquals( 'Staq\\App\\Starter', $app->get_namespace( ) );
		$this->assertEquals( 'local', $app->get_platform( ) );
		$this->assertEquals( '/bou' , $app->get_current_uri( ) );
	}

	public function test_application_n_platform_switcher__match_application_n_platform( ) {
		$this->get_request_url( 'http://localhost/local/noconf/bou' );
		$app = ( new \Staq\Server )
			->add_application( $this->get_project_class( 'NoConfiguration' ), '/noconf' )
			->add_platform( 'local', '/local')
			->launch( );
		$this->assertEquals( $this->get_project_class( 'NoConfiguration' ), $app->get_namespace( ) );
		$this->assertEquals( 'local', $app->get_platform( ) );
		$this->assertEquals( '/bou' , $app->get_current_uri( ) );
	}
}
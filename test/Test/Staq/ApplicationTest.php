<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

echo 'Staq ' . \Staq::VERSION . ' tested with ';

class ApplicationTest extends StaqTestCase {



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
	  EXTENSIONS TEST METHODS             
	 *************************************************************************/
	public function test_empty_project__extensions( ) {
		$app = \Staq\App::create( )
			->set_platform( 'local' );
		$this->assertEquals( $this->starter_namespaces, $app->get_extension_namespaces( ) );
	}

	public function test_empty_project__platform__default( ) {
		$app = \Staq\App::create( );
		$this->assertEquals( 'prod', $app->get_platform( ) );
	}

	public function test_empty_project__platform__setted( ) {
		$app = \Staq\App::create( )
			->set_platform( 'local' );
		$this->assertEquals( 'local', $app->get_platform( ) );
	}

	public function test_no_configuration__extensions( ) {
		$project_namespace = $this->get_project_class( 'NoConfiguration' );
		$app = \Staq\App::create( $project_namespace )
			->set_platform( 'local' );
		$expected = $this->append_project_namespace( 'NoConfiguration' );
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}

	public function test_simple_configuration__extensions( ) {
		$project_namespace = $this->get_project_class( 'SimpleConfiguration' );
		$app = \Staq\App::create( $project_namespace )
			->set_platform( 'local' );
		$expected = $this->append_project_namespace( 'SimpleConfiguration' );
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}

	public function test_extend_no_configuration__extensions( ) {
		$project_namespace = $this->get_project_class( 'ExtendNoConfiguration' );
		$app = \Staq\App::create( $project_namespace )
			->set_platform( 'local' );
		$expected = $this->append_project_namespace( 'ExtendNoConfiguration', 'NoConfiguration' );
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}

	public function test_without_starter__extensions( ) {
		$project_namespace = $this->get_project_class( 'WithoutStarter' );
		$app = \Staq\App::create( $project_namespace )
			->set_platform( 'local' );
		$expected = [ $project_namespace, 'Staq\Core\Ground' ];
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}




	/*************************************************************************
	  EXTENSIONS TEST METHODS             
	 *************************************************************************/
	public function test_error_reporting__none( ) {
		$app = \Staq\App::create( );
		$this->assertEquals( 0, ini_get( 'error_reporting' ) );
	}

	public function test_error_reporting__display( ) {
		$this->setExpectedException( 'PHPUnit_Framework_Error' );
		$app = \Staq\App::create( )
			->set_platform( 'local' );
		$this->assertEquals( E_ALL, ini_get( 'error_reporting' ) );
		trigger_error( 'Test of warnings', E_USER_ERROR );
	}
}
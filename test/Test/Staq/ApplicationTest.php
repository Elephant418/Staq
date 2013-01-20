<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

echo 'Staq ' . \Staq::VERSION . ' tested with ';

class ApplicationTest extends \PHPUnit_Framework_TestCase {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $starter_namespaces = [ 'Staq\App\Starter', 'Staq\Core\Router', 'Staq\Core\Ground' ];




	/*************************************************************************
	  UTIL METHODS             
	 *************************************************************************/
	public function get_project_namespace( $name ) {
		return 'Test\\Staq\\Project\\Application\\' . $name;
	}

	public function append_project_namespace( $name ) {
		$names = array_map( [ $this, 'get_project_namespace' ], func_get_args( ) );
		return array_merge( $names, $this->starter_namespaces );
	}




	/*************************************************************************
	  TEST METHODS             
	 *************************************************************************/
	public function test_empty_project__extensions( ) {
		$app = \Staq\Application::create( );
		$this->assertEquals( $this->starter_namespaces, $app->get_extension_namespaces( ) );
	}

	public function test_empty_project__platform( ) {
		$app = \Staq\Application::create( );
		$this->assertEquals( 'prod', $app->get_platform( ) );
	}

	public function test_no_configuration__extensions( ) {
		$project_namespace = $this->get_project_namespace( 'NoConfiguration' );
		$app = \Staq\Application::create( $project_namespace );
		$expected = $this->append_project_namespace( 'NoConfiguration' );
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}

	public function test_simple_configuration__extensions( ) {
		$project_namespace = $this->get_project_namespace( 'SimpleConfiguration' );
		$app = \Staq\Application::create( $project_namespace );
		$expected = $this->append_project_namespace( 'SimpleConfiguration' );
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}

	public function test_extend_no_configuration__extensions( ) {
		$project_namespace = $this->get_project_namespace( 'ExtendNoConfiguration' );
		$app = \Staq\Application::create( $project_namespace );
		$expected = $this->append_project_namespace( 'ExtendNoConfiguration', 'NoConfiguration' );
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}

	public function test_without_starter__extensions( ) {
		$project_namespace = $this->get_project_namespace( 'WithoutStarter' );
		$app = \Staq\Application::create( $project_namespace );
		$expected = [ $project_namespace, 'Staq\Core\Ground' ];
		$this->assertEquals( $expected, $app->get_extension_namespaces( ) );
	}
}
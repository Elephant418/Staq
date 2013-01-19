<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class ApplicationTest extends \PHPUnit_Framework_TestCase {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $starter_namespaces = [ 'Staq\App\Starter', 'Staq\Core\Router', 'Staq\Core\Ground' ];




	/*************************************************************************
	  UTIL METHODS             
	 *************************************************************************/
	public function get_project_namespace( $name ) {
		return 'Test\\Staq\\Project\\' . $name;
	}




	/*************************************************************************
	  TEST METHODS             
	 *************************************************************************/
	public function test_empty_project__extensions( ) {
		$app = \Staq\Application::create( );
		$this->assertEquals( $this->starter_namespaces, $app->get_extensions( 'namespace' ) );
	}

	public function test_empty_project__platform( ) {
		$app = \Staq\Application::create( );
		$this->assertEquals( 'prod', $app->get_platform( ) );
	}

	public function test_named_project__extensions( ) {
		$project_namespace = $this->get_project_namespace( 'Empty' );
		$app = \Staq\Application::create( $project_namespace );
		$expected = $this->starter_namespaces;
		array_unshift( $expected, $project_namespace );
		$this->assertEquals( $expected, $app->get_extensions( 'namespace' ) );
	}
}
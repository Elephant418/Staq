<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class Application_Test extends \PHPUnit_Framework_TestCase {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $starter_namespaces = [ 'Staq\App\Starter', 'Staq\Core\Router', 'Staq\Core\Ground' ];




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
		$project_namespace = 'Staq';
		$app = \Staq\Application::create( $project_namespace );
		$expected = $this->starter_namespaces;
		array_unshift( $expected, $project_namespace );
		$this->assertEquals( $expected, $app->get_extensions( 'namespace' ) );
	}
}
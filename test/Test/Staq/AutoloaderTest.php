<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class AutoloaderTest extends \PHPUnit_Framework_TestCase {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/




	/*************************************************************************
	  UTIL METHODS             
	 *************************************************************************/
	public function get_project_namespace( $name ) {
		return 'Test\\Staq\\Project\\Application\\' . $name;
	}




	/*************************************************************************
	  TEST METHODS             
	 *************************************************************************/
	public function test_unexisting_class__simple( ) {
		// $project_namespace = $this->get_project_namespace( 'NoConfiguration' );
		$app = \Staq\Application::create( );
		$stack = new \Stack\Coco;
		$this->assertEquals( 'Stack\\Coco', get_class( $stack ) );
		$this->assertEquals( 0, \Staq\Util::stack_height( $stack ) );
	}
	public function test_unexisting_class__complex( ) {
		$app = \Staq\Application::create( );
		$stack = new \Stack\Coco\Des\Bois;
		$this->assertEquals( 'Stack\\Coco\\Des\\Bois', get_class( $stack ) );
		$this->assertEquals( 0, \Staq\Util::stack_height( $stack ) );
	}
}
<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class AutoloaderTest extends \PHPUnit_Framework_TestCase {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $project_namespace = 'Test\\Staq\\Project\\Autoloader';



	/*************************************************************************
	  UTIL METHODS             
	 *************************************************************************/
	public function get_project_class( $name ) {
		return $this->project_namespace . '\\' . $name;
	}

	public function get_project_stack_class( $name ) {
		return $this->get_project_class( 'Stack\\' . $name );
	}



	/*************************************************************************
	  TEST METHODS             
	 *************************************************************************/
	public function test_unexisting_class__simple( ) {
		// $project_namespace = $this->get_project_namespace( 'NoConfiguration' );
		$app = \Staq\Application::create( );
		$stack = new \Stack\Unexisting\Coco;
		$this->assertEquals( 'Stack\\Unexisting\\Coco', get_class( $stack ) );
		$this->assertEquals( 0, \Staq\Util::stack_height( $stack ) );
	}

	public function test_unexisting_class__complex( ) {
		$app = \Staq\Application::create( );
		$stack = new \Stack\Unexisting\Coco\Des\Bois;
		$this->assertEquals( 'Stack\\Unexisting\\Coco\\Des\\Bois', get_class( $stack ) );
		$this->assertEquals( 0, \Staq\Util::stack_height( $stack ) );
	}

	public function test_existing_class__simple( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		$stack = new \Stack\Existing\Coco;
		$this->assertEquals( 1, \Staq\Util::stack_height( $stack ) );
		$this->assertTrue( is_a( $stack, $this->get_project_stack_class( 'Existing\\Coco' ) ) );
	}

	public function test_existing_class__complex( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		$stack = new \Stack\Existing\Coco\Des\Bois;
		$this->assertEquals( 1, \Staq\Util::stack_height( $stack ) );
		$this->assertTrue( is_a( $stack, $this->get_project_stack_class( 'Existing\\Coco' ) ) );
	}

	public function test_controller_class__unexisting( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		$stack = new \Stack\Controller\Unexisting;
		$this->assertTrue( is_a( $stack, 'Staq\Core\Router\Stack\Controller' ) );
	}

	public function test_controller_class__existing( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		$stack = new \Stack\Controller\Existing\Coco;
		$this->assertTrue( is_a( $stack, $this->get_project_stack_class( 'Controller\\Existing\\Coco' ) ) );
	}

	public function test_controller_class__extending( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		$stack = new \Stack\Controller\Existing\Coco;
		$this->assertTrue( is_a( $stack, 'Staq\Core\Router\Stack\Controller' ) );
	}

	public function test_exception_class__existing( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		$stack = new \Stack\Exception;
		$this->assertTrue( is_a( $stack, 'Staq\Core\Ground\Stack\Exception' ) );
	}

	public function test_exception_class__extending( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		$stack = new \Stack\Exception;
		$this->assertTrue( is_a( $stack, 'Exception' ) );
	}
}
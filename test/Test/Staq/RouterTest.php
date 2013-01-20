<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class RouterTest extends WebTestCase {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $project_namespace = 'Test\\Staq\\Project\\Router';




	/*************************************************************************
	  TEST METHODS             
	 *************************************************************************/
	public function test_extended_error_controller( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\Application::create( $this->project_namespace )
			->run( );
        $this->expectOutputString( 'error 404' );
	}

	public function test_anonymous_controller__magic_route( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\Application::create( $this->project_namespace )
			->add_controller( '/*', function( ) {
				return 'hello';
			})
			->run( );
        $this->expectOutputString( 'hello' );
	}

	public function test_anonymous_controller__simple_route__no_match( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\Application::create( $this->project_namespace )
			->add_controller( '/hello', function( ) {
				return 'hello';
			})
			->run( );
        $this->expectOutputString( 'error 404' );
	}

	public function test_anonymous_controller__simple_route__match( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\Application::create( $this->project_namespace )
			->add_controller( '/coco', function( ) {
				return 'hello';
			})
			->run( );
        $this->expectOutputString( 'hello' );
	}

	public function test_anonymous_controller__param_route__wrong_definition( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\Application::create( $this->project_namespace )
			->add_controller( '/:coco', function( $world ) {
				return 'hello ' . $world;
			})
			->run( );
        $this->expectOutputString( 'error 500' );
	}
}
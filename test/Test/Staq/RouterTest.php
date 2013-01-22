<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class RouterTest extends WebTestCase {




	/*************************************************************************
	  GLOBAL METHODS			 
	 *************************************************************************/
	protected function setUp( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\Application::create( $this->project_namespace );
	}




	/*************************************************************************
	  TEST METHODS             
	 *************************************************************************/
	public function test_extended_error_controller( ) {
		\Staq\Application::run( );
        $this->expectOutputString( 'error 404' );
	}

	public function test_anonymous_controller__magic_route( ) {
		\Staq\Application::add_controller( '/*', function( ) {
				return 'hello';
			})
			->run( );
        $this->expectOutputString( 'hello' );
	}

	public function test_anonymous_controller__simple_route__no_match( ) {
		\Staq\Application::add_controller( '/hello', function( ) {
				return 'hello';
			})
			->run( );
        $this->expectOutputString( 'error 404' );
	}

	public function test_anonymous_controller__simple_route__match( ) {
		\Staq\Application::add_controller( '/coco', function( ) {
				return 'hello';
			})
			->run( );
        $this->expectOutputString( 'hello' );
	}

	public function test_anonymous_controller__param_route__wrong_definition( ) {
		\Staq\Application::add_controller( '/:coco', function( $world ) {
				return 'hello ' . $world;
			})
			->run( );
        $this->expectOutputString( 'error 500' );
	}
}
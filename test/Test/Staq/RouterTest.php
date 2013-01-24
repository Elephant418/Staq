<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class RouterTest extends WebTestCase {




	/*************************************************************************
	  GLOBAL METHODS			 
	 *************************************************************************/
	protected function setUp( ) {
		parent::setUp( );
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\Application::create( $this->project_namespace )
			->set_platform( 'local' );
	}




	/*************************************************************************
	  ERROR CONTROLLER TEST METHODS             
	 *************************************************************************/
	public function test_extended_error_controller( ) {
		\Staq\Application::run( );
        $this->expectOutputHtmlContent( 'error 404' );
	}




	/*************************************************************************
	  ANONYMOUS CONTROLLER TEST METHODS             
	 *************************************************************************/
	public function test_anonymous_controller__magic_route( ) {
		\Staq\Application::add_controller( '/*', function( ) {
				return 'hello';
			})
			->run( );
        $this->expectOutputHtmlContent( 'hello' );
	}

	public function test_anonymous_controller__simple_route__no_match( ) {
		\Staq\Application::add_controller( '/hello', function( ) {
				return 'hello';
			})
			->run( );
        $this->expectOutputHtmlContent( 'error 404' );
	}

	public function test_anonymous_controller__simple_route__match( ) {
		\Staq\Application::add_controller( '/coco', function( ) {
				return 'hello';
			})
			->run( );
        $this->expectOutputHtmlContent( 'hello' );
	}

	public function test_anonymous_controller__param_route__wrong_definition( ) {
		\Staq\Application::add_controller( '/:coco', function( $world ) {
				return 'hello ' . $world;
			})
			->run( );
        $this->expectOutputHtmlContent( 'error 500' );
	}

	public function test_anonymous_controller__conditionnal_controller( ) {
		\Staq\Application::add_controller( '/*', function( ) {
				if ( \Staq\Application::get_current_uri( ) == '/coco' ) {
					return NULL;
				}
			})
			->run( );
        $this->expectOutputHtmlContent( 'error 404' );
	}




	/*************************************************************************
	  PUBLIC FILE CONTROLLER TEST METHODS             
	 *************************************************************************/
	public function test_public_controller__match( ) {
		$this->get_request_url( 'http://localhost/static.txt' );
		\Staq\Application::run( );
        $this->expectOutputHtmlContent( 'This is an example of static file' );
	}
}
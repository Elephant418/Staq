<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class ViewTest extends WebTestCase {



	/*************************************************************************
	  TEST METHODS             
	 *************************************************************************/
	public function test_text_templating__no_variable( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->project_namespace )
			->add_controller( '/*', function( ) {
				return new \Stack\View;
			} )
			->run( );
        $this->expectOutputString( 'Hello !' );
	}

	public function test_text_templating__inherit( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->project_namespace )
			->add_controller( '/*', function( ) {
				return new \Stack\View\Some\Path\That\No\Body\Knows;
			} )
			->run( );
        $this->expectOutputString( 'Hello !' );
	}

	public function test_text_templating__complex( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->project_namespace )
			->add_controller( '/*', function( ) {
				return new \Stack\View\Inherited\Template;
			} )
			->run( );
        $this->expectOutputString( 'Adios !' );
	}

	public function test_text_templating__one_variable( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->project_namespace )
			->add_controller( '/*', function( ) {
				$page = new \Stack\View;
				$page[ 'name' ] = 'world';
				return $page;
			} )
			->run( );
        $this->expectOutputString( 'Hello world!' );
	}



	/*************************************************************************
	  TWIG EXTENSION TEST METHODS             
	 *************************************************************************/
	public function test_public_filter( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->project_namespace )
			->set_base_uri( '/prefix/path' )
			->add_controller( '/*', function( ) {
				return new \Stack\View\Extension\PublicFilter;
			} )
			->run( );
        $this->expectOutputString( '/prefix/path/coco' );
	}

	public function test_public_function( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->project_namespace )
			->set_base_uri( 'prefix/path/' )
			->add_controller( '/*', function( ) {
				return new \Stack\View\Extension\PublicFunction;
			} )
			->run( );
        $this->expectOutputString( '/prefix/path/coco' );
	}

	public function test_route_function( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->project_namespace )
			->set_base_uri( '/prefix/path/' )
			->add_controller( '/*', function( ) {
				return new \Stack\View\Extension\RouteFunction;
			} )
			->run( );
        $this->expectOutputString( '/prefix/path/error/418' );
	}
}
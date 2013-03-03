<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class ViewTest extends WebTestCase {



	/*************************************************************************
	  TEST METHODS             
	 *************************************************************************/
	public function test_text_templating__no_variable( ) {
		$this->getRequestUrl( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->projectNamespace )
			->setPlatform( 'local' )
			->addController( '/*', function( ) {
				return new \Stack\View;
			} )
			->run( );
        $this->expectOutputString( 'Hello !' );
	}

	public function test_text_templating__inherit( ) {
		$this->getRequestUrl( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->projectNamespace )
			->setPlatform( 'local' )
			->addController( '/*', function( ) {
				return new \Stack\View\Some\Path\That\No\Body\Knows;
			} )
			->run( );
        $this->expectOutputString( 'Hello !' );
	}

	public function test_text_templating__complex( ) {
		$this->getRequestUrl( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->projectNamespace )
			->setPlatform( 'local' )
			->addController( '/*', function( ) {
				return new \Stack\View\Inherited\Template;
			} )
			->run( );
        $this->expectOutputString( 'Adios !' );
	}

	public function test_text_templating__one_variable( ) {
		$this->getRequestUrl( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->projectNamespace )
			->setPlatform( 'local' )
			->addController( '/*', function( ) {
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
		$this->getRequestUrl( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->projectNamespace )
			->setPlatform( 'local' )
			->setBaseUri( '/prefix/path' )
			->addController( '/*', function( ) {
				return new \Stack\View\Extension\PublicFilter;
			} )
			->run( );
        $this->expectOutputString( '/prefix/path/coco' );
	}

	public function test_public_function( ) {
		$this->getRequestUrl( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->projectNamespace )
			->setPlatform( 'local' )
			->setBaseUri( 'prefix/path/' )
			->addController( '/*', function( ) {
				return new \Stack\View\Extension\PublicFunction;
			} )
			->run( );
        $this->expectOutputString( '/prefix/path/coco' );
	}

	public function test_route_function( ) {
		$this->getRequestUrl( 'http://localhost/coco' );
		$app = \Staq\App::create( $this->projectNamespace )
			->setPlatform( 'local' )
			->setBaseUri( '/prefix/path/' )
			->addController( '/*', function( ) {
				return new \Stack\View\Extension\RouteFunction;
			} )
			->run( );
        $this->expectOutputString( '/prefix/path/error/418' );
	}
}
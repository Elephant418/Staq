<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class ViewTest extends WebTestCase {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $project_namespace = 'Test\\Staq\\Project\\View';



	/*************************************************************************
	  TEST METHODS             
	 *************************************************************************/
	public function test_text_templating__no_variable( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\Application::create( $this->project_namespace )
			->add_controller( '/*', function( ) {
				return new \Stack\View;
			} )
			->run( );
        $this->expectOutputString( 'Hello !' );
	}

	public function test_text_templating__one_variable( ) {
		$this->get_request_url( 'http://localhost/coco' );
		$app = \Staq\Application::create( $this->project_namespace )
			->add_controller( '/*', function( ) {
				$page = new \Stack\View;
				$page[ 'name' ] = 'world';
				return $page;
			} )
			->run( );
        $this->expectOutputString( 'Hello world!' );
	}
}
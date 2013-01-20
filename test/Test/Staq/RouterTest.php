<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class RouterTest extends \PHPUnit_Framework_TestCase {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $project_namespace = 'Test\\Staq\\Project\\Router';



	/*************************************************************************
	 UTIL METHODS
	 *************************************************************************/
	public function get_request_url( $url, $method = 'GET' ) {
		$_SERVER[ 'REQUEST_METHOD' ] = $method;
		$infos = parse_url( $url );
		$_SERVER[ 'HTTP_HOST' ] = $infos[ 'host' ];
		$_SERVER[ 'SERVER_NAME' ] = $infos[ 'host' ];
		$_SERVER[ 'SERVER_PORT' ] = isset( $infos[ 'port' ] ) ? $infos[ 'port' ] : 80;
		$_SERVER[ 'QUERY_STRING' ] = isset( $infos[ 'query' ] ) ? $infos[ 'query' ] : '';
		$_SERVER[ 'REQUEST_URI' ] = $infos[ 'path' ];
		parse_str( $_SERVER[ 'QUERY_STRING' ], $_GET );
	}

	public function post_request_url( $url, $post = [ ] ) {
		$this->get_request_url( $url, $method = 'GET' );
		$_POST = $post;
	}

	public function is_error_document( $code = 404 ) {
		foreach( headers_list( ) as $header ) {
			echo $header . PHP_EOL;
			if ( \UString::is_start_with( $header, 'HTTP/1.1 ' . $code ) ) {
				return TRUE;
			}
		}
		return FALSE;
	}

	public function is_redirection( $url ) {
		foreach( headers_list( ) as $header ) {
			if ( \UString::is_start_with( $header, 'Location: ' . $url ) ) {
				return TRUE;
			}
		}
		return FALSE;
	}




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
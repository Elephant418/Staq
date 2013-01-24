<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class WebTestCase extends StaqTestCase {



	/*************************************************************************
	 URL SIMULATE METHODS
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



	/*************************************************************************
	 HTML OUTPUT TEST METHODS
	 *************************************************************************/
	protected function setUp( ) {
		parent::setUp( );
		ob_start( );
	}
	
	public function expectOutputHtmlContent( $expected ) {
		$actual = ob_get_contents( );
		ob_end_clean( );
		$regex = '/<body\s?[^>]*>(.*)$/is';
		$matches = [ ];
		if ( preg_match( $regex, $actual, $matches ) ) {
			$actual = $matches[ 1 ];
		}
		$this->assertEquals( trim( strip_tags( $actual ) ), $expected );
	}



	/*************************************************************************
	 HEADERS TEST METHODS
	 *************************************************************************/
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
}
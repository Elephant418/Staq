<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack\Controller;

class Error extends Error\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $route_action = [
		'match_uri' => '/error/:code',
		'match_exceptions' => [ '404', '500' ]
	];



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function action( $code ) {
		if ( ! headers_sent( ) ) {
			if ( $code == '403' ) {
				header( 'HTTP/1.1 403 Forbidden' );
			} else if ( $code == '404' ) {
				header( 'HTTP/1.1 404 Not Found' );
			} else {
				header( 'HTTP/1.1 500 Internal Server Error' );
			}
		}
		return 'Error ' . $code . '!';
	}

}

?>
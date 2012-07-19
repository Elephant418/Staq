<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Controller;

class Error extends \Controller\__Base {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $handled_routes = [ 'view' => '/error(/:code)' ];


	/*************************************************************************
	  ACTION METHODS                   
	 *************************************************************************/
        public function view( $code = '500' ) {
		if ( $code == '403' ) {
			header( 'HTTP/1.1 403 Forbidden' );
		} else if ( $code == '404' ) {
			header( 'HTTP/1.1 404 Not Found' );
		} else {
			header( 'HTTP/1.1 500 Internal Server Error' );
		}

		$str  = '<h1>Error ' . $code . '</h1>';
		$str .= '<p>We are sorry, but there is a problem.</p>';
		foreach( \Notification::pull( \Notification::EXCEPTION ) as $message ) {
			$str .= '<p>Message : ' . $message . '</p>';
		}
		return $str; 
	}
}

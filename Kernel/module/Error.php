<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Module;

class Error extends \Module\__Base {



	/*************************************************************************
	  ATTRIBUTES				   
	 *************************************************************************/
	public $type = 'Error';



	/*************************************************************************
	  ROUTE METHODS                   
	 *************************************************************************/
	public function handle_exception( $exception ) {
		if ( isset( $exception->type ) ) {
			if ( $exception->type == 'Resource_Not_Found' ) {	
				return [ 'view', [ '404', $exception ] ];
			} else if ( $exception->type == 'Resource_Forbidden' ) {	
				return [ 'view', [ '403', $exception ] ];
			} 
		} else if ( get_class( $exception ) == 'Exception' ) {	
			return [ 'view', [ '500', $exception ] ];
		}
		return FALSE;
	}

	public function get_side_route( $side, $parameters = [ ] ) {
		
	}


	/*************************************************************************
	  SIDE METHODS                   
	 *************************************************************************/
        public function view( $code = '500', $exception = NULL ) {
		if ( $code == '403' ) {
			header( 'HTTP/1.1 403 Forbidden' );
		} else if ( $code == '404' ) {
			header( 'HTTP/1.1 404 Not Found' );
		} else {
			header( 'HTTP/1.1 500 Internal Server Error' );
		}

		$str  = '<h1>Error ' . $code . '</h1>';
		$str .= '<p>We are sorry, but there is a problem.</p>';
		if ( ! is_null( $exception ) && ( new \Settings )->by_file( 'application' )->get( 'errors', 'display_errors' ) ) {
			$str .= '<p>Technical error : ' . $exception->getMessage( ) . '</p>';
		}
		return $str; 
	}
}

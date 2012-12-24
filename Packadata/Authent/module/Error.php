<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Authent\Module;

class Error extends Error\__Parent {



	/*************************************************************************
	  VIEW METHODS                   
	 *************************************************************************/
    public function view( $code = '500', $exception = NULL ) {
		if ( $code == '403' ) {
			header( 'HTTP/1.1 403 Forbidden' );
			header( 'Location: ' . \Supersoniq\module_page_route( 'Authent', 'login' ) );
			die;
		}
		return parent::view( $code, $exception );
	}
}

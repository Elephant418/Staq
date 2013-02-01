<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

abstract class Application {



	/*************************************************************************
	  STATIC SHORTHAND METHODS                 
	 *************************************************************************/
	public static function create( $path = 'Staq\Core\Ground', $base_uri = NULL, $platform = 'prod' ) {
		if ( is_null( $base_uri ) ) {
			if ( isset( $_SERVER[ 'DOCUMENT_ROOT' ] ) && isset( $_SERVER[ 'SCRIPT_FILENAME' ] ) ) {
				if ( \UString::is_start_with( $_SERVER[ 'SCRIPT_FILENAME' ], $_SERVER[ 'DOCUMENT_ROOT' ] ) ) {
					$base_uri = \UString::not_start_with( dirname( $_SERVER[ 'SCRIPT_FILENAME' ] ), $_SERVER[ 'DOCUMENT_ROOT' ] );
				}
			}
			if ( is_null( $base_uri ) ) {
				$base_uri = '/';
			}
		}
		return ( new \Staq\Server )->create_application( $path, $base_uri, $platform );
	}
}

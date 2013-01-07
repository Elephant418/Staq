<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

abstract class Application {



	/*************************************************************************
	  STATIC SHORTHAND METHODS                 
	 *************************************************************************/
	public static function create( $path = 'Staq/core/ground', $root_uri = '/', $platform = 'prod' ) {
		return ( new \Staq\Server )->create_application( $path, $root_uri, $platform );
	}
	public static function current_application( ) {
		return \Staq\Server::$application;
	}
	public static function get_extensions( ) {
		return self::current_application( )->get_extensions( );
	}
	public static function get_platform( ) {
		return self::current_application( )->get_platform( );
	}
}

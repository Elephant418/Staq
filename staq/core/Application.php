<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

abstract class Application {



	/*************************************************************************
	  STATIC SHORTHAND METHODS                 
	 *************************************************************************/
	public static function create( $path = 'staq/core/ground', $root_uri = '/', $platform = 'prod' ) {
		return ( new \Staq\Server )->create_application( $path, $root_uri, $platform );
	}
	public static function current_application( ) {
		return \Staq\Server::$application;
	}
	public static function __callStatic( $name, $arguments ) {
		$application = self::current_application( );
		$callable = [ $application, $name ];
		if ( ! is_callable( $callable ) ) {
			$caller = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 1 );
			throw new \BadMethodCallException( 'Call to undefined method Stack\\Application::' . $name . ' in ' . $caller[ 'file' ] . ' on line ' . $caller[ 'line' ] );
		}
		return call_user_func_array( $callable, $arguments );
	}
}

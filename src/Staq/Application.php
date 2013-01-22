<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

abstract class Application {



	/*************************************************************************
	  STATIC SHORTHAND METHODS                 
	 *************************************************************************/
	public static function create( $path = 'Staq\Core\Ground', $root_uri = '/', $platform = 'prod' ) {
		return ( new \Staq\Server )->create_application( $path, $root_uri, $platform );
	}
	public static function get_current_application( ) {
		return \Staq\Server::$application;
	}
	public static function __callStatic( $name, $arguments ) {
		$application = self::get_current_application( );
		$callable = [ $application, $name ];
		if ( ! is_callable( $callable ) ) {
			$caller = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 1 )[ 0 ];
			$message = 'Call to undefined method Stack\\Application::' . $name;
			if ( isset( $caller[ 'file' ] ) ) {
				$message .= ' in ' . $caller[ 'file' ];
				if ( isset( $caller[ 'line' ] ) ) {
					$message .= ' in ' . $caller[ 'line' ];
				}
			}
			throw new \BadMethodCallException( $message );
		}
		return call_user_func_array( $callable, $arguments );
	}
}

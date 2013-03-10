<?php

/* This file is part of the Staq project, which is under MIT license */


class Staq { 

	const VERSION = '0.5';



	/*************************************************************************
	  STATIC SHORTHAND METHODS                 
	 *************************************************************************/
	public static function App( ) {
		return static::Application( );
	}

	public static function Application( ) {
		$app = \Staq\Server::$application;
		if ( count( func_get_args( ) ) === 0 ) {
			return $app;
		}
		return call_user_func_array( [ $app, 'query' ], func_get_args( ) );
	}

}

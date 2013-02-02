<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

abstract class Application {



	/*************************************************************************
	  STATIC SHORTHAND METHODS                 
	 *************************************************************************/
	public static function create( $path = 'Staq\Core\Ground', $base_uri = NULL, $platform = 'prod' ) {
		return ( new \Staq\Server )->create_application( $path, $base_uri, $platform );
	}
}

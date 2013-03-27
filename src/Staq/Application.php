<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

abstract class Application {



	/*************************************************************************
	  STATIC SHORTHAND METHODS                 
	 *************************************************************************/
	public static function create( $path = 'Staq\Core\Ground', $baseUri = NULL, $platform = NULL ) {
		return ( new \Staq\Server )->createApplication( $path, $baseUri, $platform );
	}
}

<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack\Controller;

class Error extends Error\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $route_action = [
		'match_uri' => '/error/:code',
		'match_exceptions' => [ '404', '500' ]
	];



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function action( $code ) {
		return 'Error ' . $code . '!';
	}

}

?>
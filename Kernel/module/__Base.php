<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Module;

abstract class __Base {



	/*************************************************************************
	  ATTRIBUTES                   
	 *************************************************************************/
	public $type;
	public $routes;



	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {
		$this->type = \Supersoniq\substr_after_last( get_class( $this ), '\\' );
		// TODO: get routes from settings
		// TODO: get routes from views
	}



	/*************************************************************************
	  ROUTE METHODS                   
	 *************************************************************************/
	public function handle_route( $route ) {
		return FALSE;
	}

	public function handle_exception( $exception ) {
		return FALSE;
	}

	public function get_side_route( $side, $parameters = [ ] ) {
		
	}
}


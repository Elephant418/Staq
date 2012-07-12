<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Exception;

class Redirect extends \Exception\__Base {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $route;


	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $route, $message = NULL ) {
	        parent::__construct( $message );
		$this->route = $route;
	}
}

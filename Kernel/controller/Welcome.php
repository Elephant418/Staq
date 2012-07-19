<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Controller;

class Welcome extends \Controller\__Base {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $handled_routes = [ 'view' => '/' ];


	/*************************************************************************
	  ACTION METHODS                   
	 *************************************************************************/
	public function view( ) {
		return 'This Supersoniq instance is empty.';
	}
}

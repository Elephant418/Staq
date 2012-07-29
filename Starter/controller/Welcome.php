<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Starter\Controller;

class Welcome extends \Controller\__Base {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $handled_routes = array( 
		'view' => '/'
	);


	/*************************************************************************
	  ACTION METHODS                   
	 *************************************************************************/
	public function view( ) {
		return '<h1>Hello !</h1><p>I\'m the Welcome Controller.</p><p>I will help you to start with Supersoniq :)</p>';
	}
}

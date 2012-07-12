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
	protected $handled_routes = array( 
		'view' => '/'
	);


	/*************************************************************************
	  ACTION METHODS                   
	 *************************************************************************/
	public function view( ) {
		$this->view->title   = 'Welcome';
		$this->view->content = 'Hello World';
		return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
	}
}

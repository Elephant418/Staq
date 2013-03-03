<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack;

class View extends View\__Parent {



	/*************************************************************************
	  PRIVATE METHODS              
	 *************************************************************************/
	public function add_variables( ) {
		parent::add_variables( );
		$this[ 'current_user' ] = \Staq::App()->getController( 'Auth' )->current_user( );
	}
}

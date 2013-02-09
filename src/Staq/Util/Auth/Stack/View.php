<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack;

class View extends View\__Parent {



	/*************************************************************************
	  PRIVATE METHODS              
	 *************************************************************************/
	protected function init_default_variables( ) {
		$page[ 'user_logged' ] = \Staq::App()->get_controller( 'Auth' )->is_logged( );
	}
}

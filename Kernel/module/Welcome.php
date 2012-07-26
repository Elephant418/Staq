<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Module;

class Welcome extends \Module\__Base {


	/*************************************************************************
	  ACTION METHODS                   
	 *************************************************************************/
	public function view( ) {
		return $this->get_page_view( 'view' );
	}
}

<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Module;

abstract class Model extends \__Auto\Module\__Base {



	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {
		parent::__construct( );
		$this->settings->file_type( 'module', 'model' )->load( );
	}

}

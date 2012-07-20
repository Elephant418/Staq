<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Exception;

abstract class __Base extends \Exception {



	/*************************************************************************
	  ATTRIBUTES                   
	 *************************************************************************/
	public $type;



	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( $message = NULL, $code = 0 ) {
		parent::__construct( $message, $code );
		$this->type = \Supersoniq\substr_after_last( get_class( $this ), '\\' );
	}
}


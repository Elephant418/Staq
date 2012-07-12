<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Exception;

class __Base extends \Exception {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $autoload_create_child = 'Exception\\__Base';


	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $message ) {
	        parent::__construct( $message );
		// echo $message . HTML_EOL;
	}
}

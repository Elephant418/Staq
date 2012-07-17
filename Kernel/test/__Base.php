<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Test;

class __Base {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $error;


	/*************************************************************************
	  RUN METHODS                   
	 *************************************************************************/
	public function run( ) {
		echo '<h1>' . get_class( $this ) . '</h1>';
		foreach( get_class_methods( $this ) as $method_name ) {
			if ( \Supersoniq\starts_with( $method_name, 'test_' ) ) { 
				echo '->' . $method_name . '( ) : ';
				if ( $this->$method_name( ) ) {
					 echo 'OK';
				} else {
					echo '<strong>KO</strong> ( ' . $this->error . ' )';
					unset( $this->error );	
				}
				echo HTML_EOL;
			}
		}
	}


	/*************************************************************************
	  UTILS METHODS                   
	 *************************************************************************/
	public function assert_equals( $value, $match ) {
		$return = ( $value == $match );
		if ( ! $return ) {
			$this->error = '"' . $match . '" expected but "' . $value . '" found' ;
		}
		return $return;
	}
}




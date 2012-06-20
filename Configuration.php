<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq;

class Configuration {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	private $conf = array( );



	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( $source_file_path ) {
		if ( ! file_exists( $source_file_path ) ) {
			throw new \Exception( 'Configuration source file "' . $source_file_path . '" does not exist' );
		}
		$this->conf = parse_ini_file( $source_file_path, TRUE );
	}



	/*************************************************************************
	  PUBLIC METHODS                   
	 *************************************************************************/
	public function get( $section, $property ) {
		if ( ! $this->has( $section, $property ) ) {
			return FALSE;
		}
		return $this->conf[ $section ][ $property ];
	}
	public function has( $section, $property ) {
		return isset( $this->conf[ $section ][ $property ] );
	}
}

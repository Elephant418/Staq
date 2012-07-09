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
	public function __construct( $file_name ) {
		if ( SUPERSONIQ_PLATFORM ) {
			$this->parse( $file_name . '.' . SUPERSONIQ_PLATFORM );
		}
		$this->parse( $file_name, TRUE );
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



	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	private function parse( $file_name, $required = FALSE ) {
		$source_file_path = SUPERSONIQ_ROOT_PATH . SUPERSONIQ_APPLICATION . '/conf/' . $file_name . '.ini';
		if ( file_exists( $source_file_path ) ) {
			$this->register_data( parse_ini_file( $source_file_path, TRUE ) );
		} else if ( $required ) {
			throw new \Exception( 'Configuration source file "' . $source_file_path . '" does not exist' );
		}
	}
	private function register_data( $data ) {
		foreach ( $data as $section => $properties ) {
			if ( ! isset( $this->conf[ $section ] ) ) {
				$this->conf[ $section ] = $properties;
			} else {
				foreach ( $properties as $property => $value ) {
					if ( ! isset( $this->conf[ $section ][ $property ] ) ) {
						$this->conf[ $section ][ $property ] = $value;
					}
				}
			}
		}
	}
}

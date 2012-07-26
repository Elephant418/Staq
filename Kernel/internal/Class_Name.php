<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Internal;

class Class_Name {


	/*************************************************************************
	  ATTRIBUTES                  
	 *************************************************************************/
	const OBJECT = 'Object';
	public $called_name;
	public $extension;
	public $type;
	public $name;
	public $is_parent = FALSE;


	/*************************************************************************
	  SETTER                  
	 *************************************************************************/
	public function extension( $extension ) {
		$this->extension = $extension;
		return $this;
	}
	public function type( $type ) {
		$this->type = $type;
		return $this;
	}
	public function name( $name ) {
		$this->name = $name;
		return $this;
	}


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
        public function by_name( $class_name ) {
		$this->called_name = $class_name;
		$parts = array_reverse( explode( '\\', $class_name ) );
		
		// PARENT
		if ( $parts[ 0 ] == '__Parent' ) {
			$this->is_parent = TRUE; 
			$parts = array_slice( $parts, 1 );
		}
		
		// NAME
		$this->name = $parts[ 0 ];
		
		// TYPE
		$this->type = self::OBJECT;
		if ( isset( $parts[ 1 ] ) ) {
			$this->type = $parts[ 1 ];
		}
		
		// EXTENSION
		if ( isset( $parts[ 2 ] ) ) {
			$this->extension = implode( '\\', array_slice( array_reverse( $parts ), 0, -2 ) );
		}
		return $this;
        }
        public function no_class_called( ) {
		$this->called_name = $this->get_full_class_name( );
		return $this;
        }


	/*************************************************************************
	  PUBLIC METHODS                   
	 *************************************************************************/
	public function is_object( ) {
		return ( $this->type == self::OBJECT );
	}

	public function is_parent( ) {
		return $this->is_parent;
	}

	public function is_base( ) {
		return $this->name == '__Base';
	}

	public function get_namespace( ) {
		$namespace = '';
		if ( ! is_null( $this->extension ) ) {
			$namespace .= $this->extension . '\\';
		}
		return $namespace . $this->type;
	}

	public function get_full_class_name( ) {
		return $this->get_namespace( ) . '\\' . $this->name;
	}

	public function get_file_path( ) {
		$file_path = SUPERSONIQ_ROOT_PATH . \Supersoniq\format_to_path( $this->extension ) . '/';
		return $file_path . strtolower( $this->type ) . '/' . $this->name . '.php';
	}
}

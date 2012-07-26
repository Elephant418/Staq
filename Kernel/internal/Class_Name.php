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
        public function by_object( $object ) {
		return $this->by_name( get_class( $object ) );
	}
        public function by_name( $class_name ) {
		$this->called_name = $class_name;
		$parts = explode( '\\', $class_name );
		
		// PARENT
		if ( end( $parts ) == '__Parent' ) {
			$this->is_parent = TRUE; 
			$parts = array_slice( $parts, 0, -1 );
		}
		
		// EXTENSION
		if ( count( $parts ) >= 3 ) {
			if ( $parts[ 0 ] == '__Auto' ) {
				$this->extension = NULL; 
				$parts = array_slice( $parts, 1 );
			} else {
				$extension = array_slice( $parts, 0, -2 );
				do {
					foreach ( \Supersoniq::$EXTENSIONS as $match ) {
						if ( implode( '/', $extension ) == $match ) {
							break 2;
						}
					}
					$extension = array_slice( $extension, 0, -1 );
				} while ( $extension );
				$parts = array_slice( $parts, count( $extension ) );
				$this->extension = implode( '\\', $extension );
			}
		}
		
		// TYPE
		if ( count( $parts ) >= 2 ) {
			$this->type = $parts[ 0 ]; 
			$parts = array_slice( $parts, 1 );
		} else {
			$this->type = 'Object';
		}
		
		// NAME
		$this->name = implode( '\\', $parts );
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

	public function get_name( ) {
		return \Supersoniq\substr_after_last( $this->name, '\\' );
	}


	public function get_namespace( ) {
		return \Supersoniq\substr_before_last( $this->get_full_class_name( ), '\\' );
	}

	public function get_full_class_name( ) {
		$namespace = '';
		if ( ! is_null( $this->extension ) ) {
			$namespace .= $this->extension . '\\';
		}
		return $namespace . $this->type . '\\' . $this->name;
	}

	public function get_file_path( ) {
		$file_path = SUPERSONIQ_ROOT_PATH . \Supersoniq\format_to_path( $this->extension ) . '/';
		return $file_path . strtolower( $this->type ) . '/' . $this->name . '.php';
	}
}

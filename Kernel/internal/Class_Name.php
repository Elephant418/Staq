<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Internal;

class Class_Name {
	use \Supersoniq\Kernel\Internal\Supersoniq_Getter;



	/*************************************************************************
	  ATTRIBUTES                  
	 *************************************************************************/
	const OBJECT = 'Object';
	protected $called_name;
	protected $extension;
	protected $type;
	protected $subtype;
	protected $is_parent = FALSE;
	protected $is_auto_extension = FALSE;
	
	// Calculated Attributes
	protected $is_object;
	protected $is_base;
	protected $name;
	protected $namespace;
	protected $full_class_name;
	protected $file_path;



	/*************************************************************************
	  GETTERS                   
	 *************************************************************************/
	public function get_is_object( ) {
		return ( $this->type == self::OBJECT );
	}

	public function get_is_base( ) {
		return $this->subtype == '__Base';
	}

	public function get_name( ) {
		return \Supersoniq\substr_after_last( $this->subtype, '\\' );
	}


	public function get_namespace( ) {
		return \Supersoniq\substr_before_last( $this->full_class_name, '\\' );
	}

	public function get_full_class_name( ) {
		$namespace = '';
		if ( ! is_null( $this->extension ) ) {
			$namespace .= $this->extension . '\\';
		}
		return $namespace . $this->type . '\\' . $this->subtype;
	}

	public function get_file_path( ) {
		$name_path = \Supersoniq\format_to_path( $this->subtype );
		$name      = \Supersoniq\substr_after_last( $name_path, '/' );
		$name_path = strtolower( \Supersoniq\substr_before_last( $name_path, '/' ) );
		if ( ! empty( $name_path ) ) {
			$name = $name_path . '/' . $name;
		} 
		$file_path = SUPERSONIQ_ROOT_PATH . \Supersoniq\format_to_path( $this->extension ) . '/';
		$file_path .= strtolower( $this->type ) . '/' . $name . '.php';
		return $file_path;
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
				$this->is_auto_extension = TRUE; 
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
			$this->type = self::OBJECT;
		}
		
		// NAME
		$this->subtype = implode( '\\', $parts );
		return $this;
        }

        public function no_class_called( ) {
		$this->called_name = $this->full_class_name;
		return $this;
        }
}

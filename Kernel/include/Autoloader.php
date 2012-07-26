<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel;

class Autoloader {


	/*************************************************************************
	  ATTRIBUTES                   
	 *************************************************************************/
        private $settings;
        private $library;


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
        public function init( ) {
		spl_autoload_register( [ $this, 'autoloader' ] );
		$this->settings = ( new \Settings )->by_file( 'application' );
		$this->library  = $this->settings->get_array( 'library' );
        }


	/*************************************************************************
	  PUBLIC LOAD                   
	 *************************************************************************/
        public function load( $class_type, $class_name ) {
		$class = ( new Class_Name )
			->type( $class_type )
			->name( $class_name )
			->no_class_called( );
		$this->load_implicit_class( $class );
		return $class->get_full_class_name( );
        }


	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	private function autoloader( $class_name ) {
		$class = ( new Class_Name )->by_name( $class_name );
		if ( $this->load_library_class( $class_name ) ) {
			return TRUE;
		} else if ( $class->is_parent( ) ) {
			return $this->load_parent_class( $class );
		} else if ( ! is_null( $class->extension ) ) {
			return $this->load_explicit_class( $class );
		} else {
			return $this->load_implicit_class( $class );
		}
	}


	/*************************************************************************
	  IMPLICIT PARENT LOADER                   
	 *************************************************************************/
	private function load_library_class( $class_name ) {
		if ( isset( $this->library[ $class_name ] ) ) {
			$file = SUPERSONIQ_ROOT_PATH . $this->library[ $class_name ];
			if ( is_file( $file ) ) {
				require_once( SUPERSONIQ_ROOT_PATH . $this->library[ $class_name ] );
				$this->check_class_loaded( $class_name );
				return TRUE;
			}
		}
		return FALSE;
	}


	/*************************************************************************
	  IMPLICIT PARENT LOADER                   
	 *************************************************************************/
	private function load_parent_class( $class ) {
		if ( $this->load_existing_parent_class( $class ) ) {
			return TRUE;
		}
		if ( $this->create_magic_parent( $class ) ) {
			return TRUE;
		}
		class_alias( 'Supersoniq\Kernel\Empty_Class', $class->called_name );
		return TRUE;
	}

	private function load_existing_parent_class( $class ) {
		$is_parent_extension = FALSE;
		$original_extension = $class->extension;
		foreach ( \Supersoniq::$EXTENSIONS as $extension ) {
			if ( $is_parent_extension ) {
				$class->extension = \Supersoniq\format_to_namespace( $extension );
				if ( $this->load_existing_explicit_class( $class ) ) {
					class_alias( $class->get_full_class_name( ), $class->called_name );
					return TRUE;
				}
			} else if ( $original_extension == \Supersoniq\format_to_namespace( $extension ) ) {
				$is_parent_extension = TRUE;
			}
		}
		$class->extension = $original_extension;
		return FALSE;
	}


	/*************************************************************************
	  IMPLICIT EXTENSION LOADER                   
	 *************************************************************************/
        public function load_implicit_class( $class ) {
		if ( $this->load_existing_implicit_class( $class ) ) {
			return TRUE;
		}
		if ( $this->create_magic_class( $class ) ) {
			return TRUE;
		}
		throw new \Exception( 'Unknown class "' . $class->called_name . '"' );
        }

	private function load_existing_implicit_class( $class ) {
		foreach ( \Supersoniq::$EXTENSIONS as $extension ) {
			$class->extension = \Supersoniq\format_to_namespace( $extension );
			if ( $this->load_existing_explicit_class( $class ) ) {
				class_alias( $class->get_full_class_name( ), $class->called_name );
				return TRUE;
			}
		}
		$class->extension =  NULL;
		return FALSE;
	}


	/*************************************************************************
	  EXPLICIT EXTENSION LOADER                   
	 *************************************************************************/
	private function load_explicit_class( $class ) {
		if ( ! in_array( \Supersoniq\format_to_path( $class->extension ), \Supersoniq::$EXTENSIONS ) ) {
			throw new \Exception( 'Unknown extension "' . $class->extension . '"' );
		}
		if ( ! $this->load_existing_explicit_class( $class ) ) {
			throw new \Exception( 'Unknown class "' . $class->called_name . '"' );
		}
		return TRUE;
	}
	private function load_existing_explicit_class( $class ) {
		if ( is_file( $class->get_file_path( ) ) ) {
			require_once( $class->get_file_path( ) );
			$this->check_class_loaded( $class->get_full_class_name( ) );
			return TRUE;
		}
		return FALSE;
	}
	private function check_class_loaded( $class_name ) {
		if ( ! class_exists( $class_name ) ) {
			$classes = get_declared_classes( );
			$loaded_class = end( $classes );
			throw new \Exception\Wrong_Class_Definition( 'Wrong class definition: "' . $loaded_class . '" definition, but "' . $class_name . '" expected.' ); 
		}
	}


	/*************************************************************************
	  MAGIC CLASS CREATION                   
	 *************************************************************************/
	private function create_magic_parent( $class ) {
		if ( $this->create_magic( $class, 'magic_parents' ) ) {
			return TRUE;
		}
		return $this->create_magic_class( $class );
	}
	private function create_magic_class( $class ) {
		return $this->create_magic( $class, 'magic_classes' );
	}
	private function create_magic( $class, $property ) {
		if ( ! $class->is_object( ) && ! $class->is_base( ) ) {
			if ( $this->settings->has( $property, $class->type ) ) {
				$base_class = $this->settings->get( $property, $class->type );
				$this->create_class( $base_class, $class );
				return TRUE;
			}
		}
		return FALSE;
	}
	private function create_class( $base_class, $class ) {
		if ( $class->is_parent() ) {
			$namespace = $class->get_full_class_name( );
			$name = '__Parent';
		} else {
			$namespace = $class->get_namespace( );
			$name = $class->name;
		}
		$code = 'namespace ' . $namespace . ';' . PHP_EOL;
		$code .= 'class ' . $name . ' extends ' . $base_class . ' { }' . PHP_EOL;
		eval( $code );
	}

}

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

class Empty_Class { }

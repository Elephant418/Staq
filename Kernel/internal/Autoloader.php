<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Internal;

class Autoloader {


	/*************************************************************************
	  ATTRIBUTES                   
	 *************************************************************************/
        private $library;


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
        public function init( ) {
		spl_autoload_register( [ $this, 'autoloader' ] );
		$settings = ( new \Settings )->by_file( 'application' );
		$this->library  = $settings->get_array( 'library' );
        }


	/*************************************************************************
	  PUBLIC LOAD                   
	 *************************************************************************/
        public function load( $class_type, $class_name ) {
		$class = ( new Class_Name )
			->type( $class_type )
			->name( $class_name )
			->no_class_called( );
		if ( $this->load_implicit_class( $class ) ) {
			return $class->get_full_class_name( );
		}
		throw new \Exception( 'Unknown ' . $class_type . ' "' . $class_name . '"' );
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
		} else if ( $this->load_implicit_class( $class, TRUE ) ) {
			return TRUE;
		}
		throw new \Exception( 'Unknown class "' . $class->called_name . '"' );
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
        public function load_implicit_class( $class, $create_alias = FALSE ) {
		if ( $this->load_existing_implicit_class( $class, $create_alias ) ) {
			return TRUE;
		}
		if ( $this->create_magic_class( $class ) ) {
			return TRUE;
		}
        }

	private function load_existing_implicit_class( $class, $create_alias ) {
		foreach ( \Supersoniq::$EXTENSIONS as $extension ) {
			$class->extension = \Supersoniq\format_to_namespace( $extension );
			if ( $this->load_existing_explicit_class( $class ) ) {
				if ( $create_alias ) {
					class_alias( $class->get_full_class_name( ), $class->called_name );
				}
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
			$settings = ( new \Settings )->by_file( 'application' );
			if ( $settings->has( $property, $class->type ) ) {
				$base_name = $settings->get( $property, $class->type );
				$base_class = '\\' . $this->load( $class->type, $base_name );
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
		} else if ( $class->is_auto_extension() ) {
			$namespace = '__Auto\\' . $class->get_namespace( );
			$name = $class->get_name( );
		} else {
			$namespace = $class->get_namespace( );
			$name = $class->get_name( );
		}
		$code = 'namespace ' . $namespace . ';' . PHP_EOL;
		$code .= 'class ' . $name . ' extends ' . $base_class . ' { }' . PHP_EOL;
		eval( $code );
	}

}

class Empty_Class { }

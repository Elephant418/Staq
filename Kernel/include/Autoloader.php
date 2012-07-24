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
		spl_autoload_register( [ $this, 'loader' ] );
		$this->settings = ( new \Settings )->by_file( 'application' );
		$this->library  = $this->settings->get_array( 'library' );
        }


	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	private function loader( $class ) {
		$split = $this->split_class( $class );

		// External library
		if ( isset( $this->library[ $class ] ) ) {
			require_once( SUPERSONIQ_ROOT_PATH . $this->library[ $class ] );

		// Explicit parent extension
		} else if ( isset( $split[ 'parent' ] ) ) {
			if ( $this->load_parent_class( $split, $class ) ) {
				return TRUE;
			}
			if ( $this->create_magic_parent( $split ) ) {
				return TRUE;
			}
			class_alias( 'Supersoniq\Kernel\Empty_Class', $class );
			return TRUE;

		// Explicit extension
		} else if ( isset( $split[ 'extension' ] ) ) {
			if ( ! in_array( \Supersoniq\format_to_path( $split[ 'extension' ] ), \Supersoniq::$EXTENSIONS ) ) {
				throw new \Exception( 'Unknown extension "' . $split[ 'extension' ] . '"' );
			}
			if ( ! $this->load_class( $split ) ) {
				throw new \Exception( 'Unknown class "' . $class . '"' );
			}
			return TRUE;

		// Implicit extension
		} else {
			if ( $this->load_implicit_class( $split, $class ) ) {
				return TRUE;
			}
			if ( $this->create_magic_class( $split ) ) {
				return TRUE;
			}
			throw new \Exception( 'Unknown class "' . $class . '"' );
		}
	}


	/*************************************************************************
	  IMPLICIT PARENT LOADER                   
	 *************************************************************************/
	private function load_parent_class( $split, $class ) {
		$parents = FALSE;
		foreach ( \Supersoniq::$EXTENSIONS as $extension ) {
			if ( $parents ) {
				$split[ 'extension' ] = \Supersoniq\format_to_namespace( $extension );
				if ( $this->load_class( $split ) ) {
					class_alias( $this->join_class( $split ), $class );
					return TRUE;
				}
			} else if ( $split[ 'extension' ] == \Supersoniq\format_to_namespace( $extension ) ) {
				$parents = TRUE;
			}
		}
		return FALSE;
	}


	/*************************************************************************
	  IMPLICIT EXTENSION LOADER                   
	 *************************************************************************/
	private function load_implicit_class( $split, $class ) {
		foreach ( \Supersoniq::$EXTENSIONS as $extension ) {
			$split[ 'extension' ] = \Supersoniq\format_to_namespace( $extension );
			if ( $this->load_class( $split ) ) {
				class_alias( $this->join_class( $split ), $class );
				return TRUE;
			}
		}
		return FALSE;
	}


	/*************************************************************************
	  EXPLICIT EXTENSION LOADER                   
	 *************************************************************************/
	private function load_class( $split ) {
		$file_path = SUPERSONIQ_ROOT_PATH . \Supersoniq\format_to_path( $split[ 'extension' ] ) . '/';
		$file_path .= strtolower( $split[ 'type' ] ) . '/' . $split[ 'name' ] . '.php';
		if ( is_file( $file_path ) ) {
			require_once( $file_path );
			$this->check_class_loaded( $split );
			return TRUE;
		}
		return FALSE;
	}
	private function check_class_loaded( $split ) {
		$class = $this->join_class( $split );
		if ( ! class_exists( $class ) ) {
			$classes = get_declared_classes( );
			$loaded_class = end( $classes );
			throw new \Exception\Wrong_Class_Definition( 'Wrong class definition: "' . $loaded_class . '" definition, but "' . $class . '" expected.' ); 
		}
	}


	/*************************************************************************
	  MAGIC CLASS CREATION                   
	 *************************************************************************/
	private function create_magic_parent( $split ) {
		if ( $this->create_magic( $split, 'magic_parents' ) ) {
			return TRUE;
		}
		return $this->create_magic_class( $split );
	}
	private function create_magic_class( $split ) {
		return $this->create_magic( $split, 'magic_classes' );
	}
	private function create_magic( $split, $property ) {
		if ( $split[ 'type' ] != 'Object' && $split[ 'name' ] != '__Base' ) {
			if ( $this->settings->has( $property, $split[ 'type' ] ) ) {
				$base_class = $this->settings->get( $property, $split[ 'type' ] );
				$this->create_class( $base_class, $split );
				return TRUE;
			}
		}
		return FALSE;
	}
	private function create_class( $base_class, $split ) {
		$name = array_pop( $split );
		$code = 'namespace ' . implode( '\\', $split ) . ';' . PHP_EOL;
		$code .= 'class ' . $name . ' extends ' . $base_class . ' { }' . PHP_EOL;
		eval( $code );
	}


	/*************************************************************************
	  UTILS METHODS                   
	 *************************************************************************/
	private function split_class( $class ) {
		$split = [ ];	
		$parts = array_reverse( explode( '\\', $class ) );
		if ( $parts[ 0 ] == '__Parent' ) {
			$split[ 'parent' ] = '__Parent'; 
			$parts = array_slice( $parts, 1 );
		}
		$split[ 'name' ] = $parts[ 0 ];
		$split[ 'type' ] = 'Object';
		if ( isset( $parts[ 1 ] ) ) {
			$split[ 'type' ] = $parts[ 1 ];
		}
		if ( isset( $parts[ 2 ] ) ) {
			$split[ 'extension' ] = implode( '\\', array_slice( array_reverse( $parts ), 0, -2 ) );
		}
		$split = array_reverse( $split );
		return $split;
	}

	private function join_class( $split ) {
		return $split[ 'extension' ] . '\\' . $split[ 'type' ] . '\\' . $split[ 'name' ];
	}

}

class Empty_Class { }

<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel;

class Autoloader {


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
        public function init( ) {
		spl_autoload_register( array( $this, 'loader' ) );
        }


	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	private function loader( $class_name ) {
		$split = $this->split_class_name( $class_name );

		// Explicit extension
		if ( isset( $split[ 'extension' ] ) ) {
			if ( ! in_array( \Supersoniq\format_to_path( $split[ 'extension' ] ), \Supersoniq::$EXTENSIONS ) ) {
				throw new \Exception( 'Unknown extension "' . $split[ 'extension' ] . '"' );
			}
			if ( ! $this->load_class( $split ) ) {
				throw new \Exception( 'Unknown class name "' . $class_name . '"' );
			}
			return TRUE;

		// Implicit extension
		} else {
			if ( $this->load_implicit_class( $split, $class_name ) ) {
				return TRUE;
			}
			if ( $this->create_magic_class( $split ) ) {
				return TRUE;
			}
			throw new \Exception( 'Unknown class name "' . $class_name . '"' );
		}
	}


	/*************************************************************************
	  IMPLICIT EXTENSION LOADER                   
	 *************************************************************************/
	private function load_implicit_class( $split, $class ) {
		foreach ( \Supersoniq::$EXTENSIONS as $extension ) {
			$split[ 'extension' ] = \Supersoniq\format_to_namespace( $extension );
			if ( $this->load_class( $split ) ) {
				class_alias( $this->join_class_name( $split ), $class );
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
		$class = $this->join_class_name( $split );
		if ( ! class_exists( $class ) ) {
			$classes = get_declared_classes( );
			$loaded_class = end( $classes );
			throw new \Exception\Wrong_Class_Definition( 'Wrong class definition: "' . $loaded_class . '" definition, but "' . $class . '" expected.' ); 
		}
	}


	/*************************************************************************
	  MAGIC CLASS CREATION                   
	 *************************************************************************/
	private function create_magic_class( $split ) {
		if ( $split[ 'type' ] != 'Object' && $split[ 'name' ] != '__Base' ) {
			$settings = ( new \Supersoniq\Kernel\Object\Settings )
				->by_file( 'application' );
			if ( $settings->has( 'magic_classes', $split[ 'type' ] ) ) {
				$base_class = $settings->get( 'magic_classes', $split[ 'type' ] );
				$this->create_class( $base_class, $split );
				return TRUE;
			}
		}
		return FALSE;
	}
	private function create_class( $base_class, $split ) {
		$code = 'namespace ' . $split[ 'type' ] . ';' . PHP_EOL;
		$code .= 'class ' . $split[ 'name' ] . ' extends ' . $base_class . ' { }' . PHP_EOL;
		eval( $code );
	}


	/*************************************************************************
	  UTILS METHODS                   
	 *************************************************************************/
	private function split_class_name( $class_name ) {
		$split = array( );	
		$parts = array_reverse( explode( '\\', $class_name ) );
		$split[ 'name' ] = $parts[ 0 ];
		$split[ 'type' ] = 'Object';
		if ( isset( $parts[ 1 ] ) ) {
			$split[ 'type' ] = $parts[ 1 ];
		}
		if ( isset( $parts[ 2 ] ) ) {
			$split[ 'extension' ] = implode( '\\', array_slice( array_reverse( $parts ), 0, -2 ) );
		}
		return $split;
	}

	private function join_class_name( $split ) {
		return $split[ 'extension' ] . '\\' . $split[ 'type' ] . '\\' . $split[ 'name' ];
	}

}

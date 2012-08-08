<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Internal;

trait Supersoniq_Getter {



	/*************************************************************************
	  GETTER
	 *************************************************************************/
	public function __get( $name ) {
		return $this->get( $name );
	}

	public function get( $name ) {
		if ( in_array( 'get_' . $name, get_class_methods( $this ) ) ) {
			return call_user_func( [ $this, 'get_' . $name ] );
		}
		if ( in_array( $name, array_keys( get_object_vars( $this ) ) ) ) {
			return $this->$name;
		}
		throw new \Exception( 'Attribute not found "' . get_class( $this ) . '::' . $name . '"' );
	}



	/*************************************************************************
	  SETTER
	 *************************************************************************/
	public function __set( $name, $value ) {
		return $this->set( $name, $value );
	}

	public function set( $name, $value ) {
		if ( in_array( 'set_' . $name, get_class_methods( $this ) ) ) {
			call_user_func( [ $this, 'set_' . $name ], $value );
			return $this;
		}
		if ( in_array( $name, array_keys( get_object_vars( $this ) ) ) ) {
			$this->$name = $value;
			return $this;
		}
		throw new \Exception( 'Attribute not found "' . get_class( $this ) . '::' . $name . '"' );
	}



	/*************************************************************************
	  GETTER & SETTER
	 *************************************************************************/
	public function __call( $name, $arguments ) {
		if ( \Supersoniq\starts_with( $name, 'get_' ) ) {
			return $this->get( substr( $name, 4 ) );
		}
		if ( \Supersoniq\starts_with( $name, 'set_' ) ) {
			$value = NULL;
			if ( ! empty( $arguments ) ) {
				$value = $arguments[ 0 ];
			}
			return $this->set( substr( $name, 4 ), $value );
		}
		throw new \Exception( 'Method not found "' . get_class( $this ) . '::' . $name . '( )"' );
	}
}

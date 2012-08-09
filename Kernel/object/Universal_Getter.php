<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Object;

trait Universal_Getter {



	/*************************************************************************
	  GETTER
	 *************************************************************************/
	public function __get( $name ) {
		return $this->get( $name );
	}

	public function get( $name ) {
		if ( $method_name = $this->is_there_a_getter_method( $name ) ) {
			return call_user_func( [ $this, $method_name ] );
		}
		if ( $this->is_there_a_public_attribute( $name ) ) {
			return $this->$name;
		}

		// Error cases
		if ( $this->is_there_an_attribute( $name ) ) {
			throw new \Exception( 'Attribute is private "' . get_class( $this ) . '::' . $name . '"' );
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
		if ( $method_name = $this->is_there_a_setter_method( $name ) ) {
			call_user_func( [ $this, $method_name ], $value );
			return $this;
		}
		if ( $this->is_there_a_public_attribute( $name ) ) {
			$this->$name = $value;
			return $this;
		}

		// Error cases
		if ( $this->is_there_an_attribute( $name ) ) {
			throw new \Exception( 'Attribute is private "' . get_class( $this ) . '::' . $name . '"' );
		}
		if ( $this->is_there_a_getter_method( $name ) ) {
			throw new \Exception( 'Attribute not writable "' . get_class( $this ) . '::' . $name . '"' );
		}
		throw new \Exception( 'Attribute not found "' . get_class( $this ) . '::' . $name . '"' );
	}



	/*************************************************************************
	  GETTER & SETTER
	 *************************************************************************/
	public function __call( $method_name, $arguments ) {
		if ( $name = $this->is_a_getter_method( $method_name ) ) {
			return $this->get( $name );
		}
		if ( $name = $this->is_a_setter_method( $method_name ) ) {
			if ( empty( $arguments ) ) {
				throw new \Exception( 'One argument expected for "' . get_class( $this ) . '::' . $method_name . '( )"' );
			}
			$value = $arguments[ 0 ];
			return $this->set( $name, $value );
		}
		throw new \Exception( 'Method not found "' . get_class( $this ) . '::' . $method_name . '( )"' );
	}



	/*************************************************************************
	  PRIVATE METHODS
	 *************************************************************************/
	private function is_a_getter_method( $method_name ) {
		if ( \Supersoniq\starts_with( $method_name, 'get_' ) ) {
			return $this->get( substr( $method_name, 4 ) );
		}
		return FALSE;
	}

	private function is_there_a_getter_method( $name ) {
		if ( in_array( 'get_' . $name, get_class_methods( $this ) ) ) {
			return 'get_' . $name;
		}
		return FALSE;
	}

	private function is_a_setter_method( $method_name ) {
		if ( \Supersoniq\starts_with( $method_name, 'set_' ) ) {
			return $this->get( substr( $method_name, 4 ) );
		}
		return FALSE;
	}

	private function is_there_a_setter_method( $name ) {
		if ( in_array( 'set_' . $name, get_class_methods( $this ) ) ) {
			return 'set_' . $name;
		}
		return FALSE;
	}

	private function is_there_an_attribute( $name ) {
		return in_array( $name, array_keys( get_object_vars( $this ) ) );
	}

	private function is_there_a_public_attribute( $name ) {
		return ( 
			$this->is_there_an_attribute( $name ) &&
			! \Supersoniq\starts_with( $name, '_' )
		);
	}
}

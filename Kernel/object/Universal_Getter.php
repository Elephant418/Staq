<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Object;

trait Universal_Getter {



	/*************************************************************************
	  ATTRIBUTES
	 *************************************************************************/
	public static $intialized = [ ];
	public static $attributes = [ ];
	public static $ATTRIBUTE = 1;
	public static $METHOD = 2;


	/*************************************************************************
	  GETTER
	 *************************************************************************/
	public function __get( $name ) {
		return $this->get( $name );
	}

	public function get( $name ) {
		$action = $this->get_action_attribute( $name, 'get' );
		if ( $action == self::$METHOD ) {
			return call_user_func( [ $this, 'get_' . $name ] );
		} 
		if ( $action == self::$ATTRIBUTE ) {
			return $this->$name;
		}

		// Error cases
		throw new \Exception( 'Attribute not found "' . get_class( $this ) . '::' . $name . '"' );
	}



	/*************************************************************************
	  SETTER
	 *************************************************************************/
	public function __set( $name, $value ) {
		return $this->set( $name, $value );
	}

	public function set( $name, $value ) {
		$action = $this->get_action_attribute( $name, 'set' );
		if ( $action == self::$METHOD ) {
			call_user_func( [ $this, 'set_' . $name ], $value );
			return $this;
		} 
		if ( $action == self::$ATTRIBUTE ) {
			$this->$name = $value;
			return $this;
		}

		// Error cases
		throw new \Exception( 'Attribute not found "' . get_class( $this ) . '::' . $name . '"' );
	}



	/*************************************************************************
	  PRIVATE METHODS
	 *************************************************************************/
	private function get_action_attribute( $name, $context ) {
		$class = get_class( $this );
		if ( ! isset( self::$intialized[ $class ] ) ) {
			$attributes = [ ];
			foreach ( array_keys( get_object_vars( $this ) ) as $var_name ) {
				if ( ! \Supersoniq\starts_with( $var_name, '_' ) ) {
					$attributes[ $var_name ][ 'get' ] = self::$ATTRIBUTE;
					$attributes[ $var_name ][ 'set' ] = self::$ATTRIBUTE;
				}
			}
			foreach ( get_class_methods( $this ) as $method_name ) {
				if ( \Supersoniq\starts_with( $method_name, 'get_' ) ) {
					$attributes[ substr( $method_name, 4 ) ][ 'get' ] = self::$METHOD;
				} else if ( \Supersoniq\starts_with( $method_name, 'set_' ) ) {
					$attributes[ substr( $method_name, 4 ) ][ 'set' ] = self::$METHOD;
				}
			}
			self::$attributes[ $class ] = $attributes;
			self::$intialized[ $class ] = TRUE;
		} else {
			$attributes = self::$attributes[ $class ];
		}
		if ( isset( $attributes[ $name ][ $context ] ) ) {
			return $attributes[ $name ][ $context ];
		}
	}
}

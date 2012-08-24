<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Object;

abstract class Object_List implements \ArrayAccess, \Iterator, \Countable {



	/*************************************************************************
	 ATTRIBUTES
	*************************************************************************/
	private $position;
	private $data = [ ];



	/*************************************************************************
	 CONSTRUCTOR
	*************************************************************************/
	public function __construct( $data = [ ] ) {
		$this->position = 0;
		$this->data = $data;
	}



	/*************************************************************************
	 ARRAY ACCESS METHODS 
	*************************************************************************/
	public function offsetExists( $offset ) {
		return isset( $this->data[ $offset ] );
	}

	public function offsetUnset($offset) {
		unset( $this->data[$offset] );
	}

	public function offsetGet( $offset ) {
		return isset( $this->data[ $offset ] ) ? $this->data[ $offset ] : NULL;
	}

	public function offsetSet( $offset, $data ) {
		if ( is_null( $offset ) ) {
			$this->data[ ] = $data;
		} else {
			$this->data[ $offset ] = $data;
		}
	}



	/*************************************************************************
	 ITERATOR METHODS 
	*************************************************************************/
	public function current( ) {
		return current( $this->data );
	}
	public function key( ) {
		return key( $this->data );
	}
	public function next( ) {
		return next( $this->data );
	}
	public function rewind( ) {
		reset( $this->data );
	}
	public function valid( ) {
		return $this->current( ) !== FALSE;
	}



	/*************************************************************************
	 COUNTABLE METHODS 
	*************************************************************************/
	public function count( ) {
		return count( $this->data );
	}



	/*************************************************************************
	 SPECIFIC GETTER & SETTER
	*************************************************************************/
	public function keys( ) {
		return array_keys( $this->data );
	}

	public function __get( $field ) {
		return $this->get( $field );
	}

	public function get( $field ) {
		$result = new $this;
		$is_database_item_list = TRUE;
		if ( is_callable( $field ) ) {
			$getter = $field;
		} else {
			$getter = function ( $item, $result ) use ( $field ) {
				$value = $item->$field;
				if ( ! is_null( $value ) ) {
					if ( \Supersoniq\is_object_list( $value ) ) {
						$result->merge_unique_object( $value );
					} else if ( is_array( $value ) ) {
						$result->merge_unique( $value );
					} else {
						$result->add_unique( $value );
					}
				}
				return $result;
			};
		}
		foreach( $this->data as $item ) {
			$result = $getter( $item, $result );
		}
		return $result;
	}

	public function get_object_type( ) {
		if ( $this->count( ) > 0 ) {
			return \Supersoniq\class_type( $this->current( ) );
		}
		return NULL;
	}

	public function get_object_subtype( ) {
		return $this->get( function( $item, $subtype ) {
			$item_subtype = \Supersoniq\class_subtype( $item );
			return $item_subtype;
		} );
	}

	public function __set( $field, $value ) {
		return $this->set( $field, $value );
	}

	public function set( $field, $value ) {
		foreach( $this->data as $item ) {
			$item->$field = $value;
		}
		return $this;
	}

	public function add_unique( $value ) {
		return $this->merge_unique( [ $value ] );
	}

	public function merge_unique( $values ) {
		$this->data = \Supersoniq\array_merge_unique( $this->data, $values );
		return $this;
	}

	public function merge_unique_object( $values ) {
		$data = [ ];
		foreach ( array_merge( $this->data, $values->to_array( ) ) as $object ) {
			$data[ $object->id ] = $object;
		}
		$this->data = $data;
		return $this;
	}



	/*************************************************************************
	 SPECIFIC GETTER & SETTER
	*************************************************************************/
	public function __call( $name, $arguments ) {
		foreach( $this->data as $item ) {
			call_user_func_array( [ $item, $name ], $arguments );
		}
		return $this;
	}
	


	/*************************************************************************
	 METHODS TO EXTEND
	*************************************************************************/
	public function filter( $condition, $value = NULL ) {
		if ( is_string( $condition ) ) {
			$field = $condition;
			$condition = function( $item ) use ( $field, $value ) {
				return ( $item->$field == $value );
			};
		}
		$filtered_data = [ ];
		foreach( $this->data as $key => $item ) {
			if ( $condition( $item ) ) {
				$filtered_data[ $key ] = $item;
			}
		}
		return new $this( $filtered_data );
	}
	public function filter_name_contains( $value ) {
		return $this->filter( function( $item ) use ( $value ) {
			return ( \Supersoniq\i_contains( $item->name( ), $value ) );
		} );
	}

	public function sort( $field, $reverse = FALSE ) {
		if ( is_callable( $field ) ) {
			$comp = $field;
		} else {
			$reverse = $reverse ? -1 : 1;
			$comp = function ( $a, $b ) use ( $field, $reverse ) {
				return ( strcasecmp( $a->$field, $b->$field ) * $reverse );
			};
		}
		$data = $this->data;
		usort( $data, $comp );
		return new $this( $data );
	}

	public function group( $field ) {
		$groups = [ ];
		foreach ( $this->data as $key => $item ) {
			if ( ! is_null( $item->$field ) && ! is_object( $item->$field ) ) {
				$groups[ $item->$field ][ $key ] = $item;
			}
		}
		foreach ( $groups as $key => $group ) {
			$groups[ $key ] = new $this( $group );
		}
		return $groups;
	}


	/*************************************************************************
	 FORMAT METHODS
	*************************************************************************/
	public function to_array( ) {
		return $this->data;
	}

	public function debug( ) {
		$elements = [ ];
		foreach ( $this->data as $data ) {
			$elements[ ] = $data->debug( );
		}
		return 'Object_List[ ' . implode( ', ', $elements ) . ' ]';
	}

	public function slice( $start, $end = NULL ) {
		if ( is_null( $end ) ) {
			$data = array_slice( $this->data, $start );
		} else {
			$data = array_slice( $this->data, $start, $end );
		}
		return new $this( $data );
	}

	public function is_empty( ) {
		return empty( $this->data );
	}
}

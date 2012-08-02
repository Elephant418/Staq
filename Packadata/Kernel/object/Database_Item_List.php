<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Object;

abstract class Database_Item_List implements \ArrayAccess, \Iterator, \Countable {



	/*************************************************************************
	 ATTRIBUTES
	*************************************************************************/
	private $position;
	private $data = [ ];



	/*************************************************************************
	 CONSTRUCTOR
	*************************************************************************/
	public function __construct( $data = NULL ) {
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
	public function __get( $field ) {
		return $this->get( $field );
	}

	public function get( $field ) {
		$values = [ ];
		foreach( $this->data as $item ) {
			if ( $item->$field != NULL ) {
				$values[ ] = $item->$field;
			}
		}
		return array_values( array_unique(  $values ) );
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
		foreach( $this->data as $item ) {
			if ( $condition( $item ) ) {
				$filtered_data[ ] = $item;
			}
		}
		return new $this( $filtered_data );
	}

	public function sort( $field ) {
		$data = $this->data;
		$comp = function ( $a, $b ) use ( $field ) {
			return strcasecmp ( $a->$field, $b->$field );
		};
		usort( $data, $comp );
		return new $this( $data );
	}

	public function group( $field ) {
		$groups = [ ];
		foreach ( $this->data as $data ) {
			$groups[ $data->$field ][ ] = $data;
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

	public function is_empty( ) {
		return empty( $this->data );
	}
}

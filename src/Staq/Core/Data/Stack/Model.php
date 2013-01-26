<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack;

class Model extends \ArrayObject {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public $id;
	public $entity;


	/*************************************************************************
	  GETTER                 
	 *************************************************************************/
	public function exists( ) {
		return ( $this->id !== NULL );
	}



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( ) {
		$class = 'Stack\\Entity';
		$sub_query = \Staq\Util::stack_sub_query( $this );
		if ( $sub_query ) {
			$class .= '\\' . $sub_query;
		}
		$this->entity = new $class;
	}


	/*************************************************************************
	  INITIALIZATION          
	 *************************************************************************/
	public function by_data( $data ) {
		\UArray::do_convert_to_array( $data );
		$model = new $this;
		$model->id = $this->entity->extract_id( $data );
		$model->exchangeArray( $data );
		return $model;
	}

	public function by_id( $id ) {
		return $this->by_data( $this->entity->get_data_by_id( $id ) );
	}

	public function all( ) {
		$all = [ ];
		foreach ( $this->entity->get_datas_by_fields( ) as $data ) {
			$all[ ] = $this->by_data( $data );
		}
		return $all;
	}


	/*************************************************************************
	  PUBLIC DATABASE REQUEST
	 *************************************************************************/
	public function delete( ) {
		if ( $this->entity->delete( $this ) ) {
			$this->id = NULL;
		}
		return $this;
	}

	public function save( ) {
		$this->id = $this->entity->save( $this );
		return $this;
	}


	/*************************************************************************
	  PHP MEHODS                 
	 *************************************************************************/
	public function __toString( ) {
		return get_class( $this ) . '(' . $this->id . ')';
	}
}

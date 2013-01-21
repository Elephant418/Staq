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
		$this->id = $this->entity->get_id_by_data( $data );
		$this->exchangeArray( $data );
		return $this;
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
		return $this->entity->delete( $this );
	}

	public function save( ) {
		$this->id = $this->entity->save( $this );
	}
}

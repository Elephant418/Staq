<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack;

class Model extends \ArrayObject implements \Stack\IModel {


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
		$this->entity = $this->new_entity( );
		$this->import_schema( );
	}

	protected function new_entity( ) {
		$class = 'Stack\\Entity';
		$sub_query = \Staq\Util::stack_sub_query( $this );
		if ( $sub_query ) {
			$class .= '\\' . $sub_query;
		}
		return new $class;
	}
	
	protected function import_schema( ) {
		$settings = ( new \Stack\Setting )->parse( $this );
		foreach ( $settings->get_as_array( 'schema' ) as $name => $setting ) {
			$this->add_attribute( $name, $setting );
		}
	}

	protected function add_attribute( $name, $setting ) {
		$attribute = ( new \Stack\Attribute )->by_setting( $this, $setting );
		parent::offsetSet( $name, $attribute );
	}


	/*************************************************************************
	  INITIALIZATION          
	 *************************************************************************/
	public function by_data( $data ) {
		\UArray::do_convert_to_array( $data );
		$model = new $this;
		$model->id = $this->entity->extract_id( $data );
		foreach ( $data as $name => $seed ) {
			$model->get_attribute( $name )->set_seed( $seed );
		}
		return $model;
	}

	public function by_id( $id ) {
		return $this->by_data( $this->entity->get_data_by_id( $id ) );
	}

	public function all( $order = NULL ) {
		return $this->fetch( [ ], NULL, $order );
	}

	public function fetch( $fields = [ ], $limit = NULL, $order = NULL ) {
		$datas = $this->entity->get_datas_by_fields( $fields, $limit, $order );
		return $this->get_list_by_datas( $datas );
	}

	public function delete_all( ) {
		return $this->entity->delete( );
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

	public function extract_seeds( ) {
		$data = [ ];
		foreach( $this->attribute_names( ) as $name ) {
			$data[ $name ] = $this->get_attribute( $name )->get_seed( );
		}
		return $data;
	}


	/*************************************************************************
	  SPECIFIC MODEL ACCESSOR METHODS				   
	 *************************************************************************/
	public function get_attribute( $index ) {
		return parent::offsetGet( $index );
	}

	public function attribute_names( ) {
		return array_keys( $this->getArrayCopy( ) );
	}


	/*************************************************************************
	  HERITED ACCESSOR METHODS				   
	 *************************************************************************/
	public function get( $index, $new_val ) {
		return $this->offsetGet( $index );
	}

	public function offsetGet( $index ) {
		if ( parent::offsetExists( $index ) ) {
			$attribute = parent::offsetGet( $index );
			return $attribute->get( );
		}
	}
 
	public function set( $index, $new_val ) {
		$this->offsetSet( $index, $new_val );
		return $this;
	}
 
	public function offsetSet( $index, $new_val ) {
		if ( parent::offsetExists( $index ) ) {
			$attribute = $this->get_attribute( $index );
			$attribute->set( $new_val );
		}
	}
 
	public function offsetUnset( $index ) {
		$this->offsetSet( $index, NULL );
	}


	/*************************************************************************
	  PRIVATE MEHODS                 
	 *************************************************************************/
	protected function get_list_by_datas( $datas ) {
		$list = [ ];
		foreach ( $datas as $data ) {
			$list[ ] = $this->by_data( $data );
		}
		return $list;
	}



	/*************************************************************************
	  PHP MEHODS                 
	 *************************************************************************/
	public function __toString( ) {
		return get_class( $this ) . '(' . $this->id . ')';
	}
}

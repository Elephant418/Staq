<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack;

class Model extends \ArrayObject implements \Stack\IModel {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public $id;
	protected $schema_attribute_names = [ ];
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
		$this->setFlags( \ArrayObject::ARRAY_AS_PROPS );
		$this->entity = $this->new_entity( );
		$this->import_schema( );
	}

	protected function new_entity( ) {
		$class = 'Stack\\Entity';
		$sub_query = \Staq\Util::getStackSubQuery( $this );
		if ( $sub_query ) {
			$class .= '\\' . $sub_query;
		}
		return new $class;
	}
	
	protected function import_schema( ) {
		$settings = ( new \Stack\Setting )->parse( $this );
		foreach ( $settings->getAsArray( 'schema' ) as $name => $setting ) {
			$this->add_attribute( $name, $setting );
		}
	}

	protected function add_attribute( $name, $setting ) {
		$attribute = ( new \Stack\Attribute )->by_setting( $this, $setting );
		$this->schema_attribute_names[ ] = $name;
		parent::offsetSet( $name, $attribute );
	}

	public function keys( ) {
		return array_keys( $this->getArrayCopy( ) );
	}

	protected function initialize( ) {
		
	}


	/*************************************************************************
	  INITIALIZATION          
	 *************************************************************************/
	public function by_data( $data ) {
		\UArray::doConvertToArray( $data );
		$model = new $this;
		$model->id = $this->entity->extract_id( $data );
		foreach ( $data as $name => $seed ) {
			$attribute = $model->get_attribute( $name );
			if ( is_object( $attribute ) ) {
				$attribute->set_seed( $seed );
			} else {
				$model->set( $name, $seed );
			}
		}
		$model->initialize( );
		return $model;
	}

	public function by_id( $id ) {
		return $this->by_field( 'id', $id );
	}

	protected function by_field( $field, $value ) {
		return $this->by_data( $this->entity->get_data_by_fields( [ $field => $value ] ) );
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
		foreach( $this->keys( ) as $name ) {
			$attribute = $this->get_attribute( $name );
			if ( \Staq\Util::isStack( $attribute, 'Stack\\Attribute' ) ) {
				$attribute = $attribute->get_seed( );
			}
			$data[ $name ] = $attribute;
		}
		return $data;
	}


	/*************************************************************************
	  SPECIFIC MODEL ACCESSOR METHODS				   
	 *************************************************************************/
	public function get_attribute( $index ) {
		if ( $this->offsetExists( $index ) ) {
			return parent::offsetGet( $index );
		}
	}


	/*************************************************************************
	  HERITED ACCESSOR METHODS				   
	 *************************************************************************/
	public function get( $index ) {
		return $this->offsetGet( $index );
	}

	public function offsetGet( $index ) {
		$attribute = $this->get_attribute( $index );
		if ( \Staq\Util::isStack( $attribute, 'Stack\\Attribute' ) ) {
			return $attribute->get( );
		} else {
			return $attribute;
		}
	}
 
	public function set( $index, $new_val ) {
		$this->offsetSet( $index, $new_val );
		return $this;
	}
 
	public function offsetSet( $index, $new_val ) {
		$attribute = $this->get_attribute( $index );
		if ( \Staq\Util::isStack( $attribute, 'Stack\\Attribute' ) ) {
			$attribute->set( $new_val );
		} else {
			parent::offsetSet( $index, $new_val );
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

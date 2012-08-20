<?php

namespace Supersoniq\Packadata\Kernel\Model;

abstract class __Base extends \Database_Table {



	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public $type;
	public $type_version = 1;
	protected $_attributes = array( );
	public $attributes_version = 0;
	public $is_versioned = FALSE;



	/*************************************************************************
	  GETTER & SETTER             
	 *************************************************************************/
	public function name( ) {
		return $this->id;
	}

	// Warning: __toString() is used for sorting
	public function __toString( ) {
		return $this->type . ':' . $this->id;
	}

	public function __get( $name ) {
		return $this->get( $name );
	}

	public function get( $name ) {
		if ( ! isset( $this->_attributes[ $name ] ) ) {
			return NULL;
		}
		return $this->_attributes[ $name ]->get( );
	}

	public function __set( $name, $value ) {
		return $this->set( $name, $value );
	}

	public function set( $name, $value ) {
		$this->_attributes[ $name ] = ( new \Data_Type\Varchar )
			->set_name( $name )
			->set( $value );
		return $this;
	}

	public function attribute( $name ) {
		if ( ! isset( $this->_attributes[ $name ] ) ) {
			return NULL;
		}
		return $this->_attributes[ $name ];
	}

	public function is_a( $type ) {
		return (
			$type == $this->type || 
			\Supersoniq\starts_with( $type, $this->type . '\\' )
		);
	}



	/*************************************************************************
	  ATTRIBUTES DEFINITION METHODS
	 *************************************************************************/
	public function get_attribute_fields( $scope = 'set' ) {
		$scope = 'SCOPE_' . strtoupper( $scope );
		if ( ! defined( '\Data_Type\__Base::' . $scope ) ) {
			$scope = 'SCOPE_SET';
		}
		$scope = constant( '\Data_Type\__Base::' . $scope );
		return $this->_attributes->filter( function( $attribute ) use( $scope ) {
			return ( $attribute->scope >= $scope );
		})->keys( );
	}



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( ) {
		parent::__construct( );
		$this->type = \Supersoniq\class_type_name( $this );
		$this->_attributes = new \Object_List;
		$this->_database->table_fields = array( 'id', 'type', 'type_version', 'attributes', 'attributes_version' );
		$this->_database->table_name = 'models';
	}


	
	/*************************************************************************
	  PUBLIC LIST METHODS
	 *************************************************************************/
	public function all( ) {
		return $this->list_by_fields( [ [
			'where' => 'type=:type OR type LIKE :type_like',
			'parameters' => [ 'type' => $this->type, 'type_like' => $this->type . '\\\%' ]
		] ] );
	}

	public function one( ) {
		return $this;
	}

	public function get_archives( ) {
		$archive = new \Model_Archive( );
		return $archive->get_model_history( $this->id, $this->type );
	}

	public function by_archive( $archive ) {
		$this->init_by_data( $archive->model_attributes );
		$this->loaded_data = array( );
		return $this;
	}
	
	

	/*************************************************************************
	 PROTECTED HANDLER
	*************************************************************************/
	protected function saved_handler( ) {
		parent::saved_handler( );
		if ( $this->is_versioned ) {
			$archive = new \Model_Archive( );
			$archive->init_by_model( $this );
			$archive->save( );
		}
	}


	
	/*************************************************************************
	  EXTENDED METHODS
	 *************************************************************************/
	protected function init_by_data( $data ) {
		if ( isset( $data[ 'type' ] ) && $data[ 'type' ] != $this->type ) {
			throw new \Exception( 'Try to initialize a "' . $this->type . '" model with "' . $data[ 'type' ] . '" data.' );
		}
		if ( isset( $data[ 'attributes' ] ) ) {
			$data[ 'attributes' ] = $this->init_attributes_by_data( $data );
		}
		return parent::init_by_data( $data );
	}

	protected function init_attributes_by_data( $data ) {
		$attributes = unserialize( $data[ 'attributes' ] );
		if ( is_array( $attributes ) ) {
			foreach ( $attributes as $name => $value ) {
				$this->_attributes[ $name ] = ( new \Data_Type\Varchar )
					->set_name( $name )
					->init( $value );
			}
		}
		return serialize( $attributes );
	}

	protected function table_fields_value( $field_name, $field_value = NULL ) {
		if ( $field_name == 'type' || $field_name == 'type_version' || $field_name == 'attributes_version' ) {
			return $this->$field_name;
		} else if ( $field_name == 'attributes' ) {
			return $this->table_attributes_value( );
		}
		return parent::table_fields_value( $field_name );
	}

	public function table_attributes_value( ) {
		$attributes = array( );
		foreach( $this->get_attribute_fields( 'none' ) as $name ) {
			$value = $this->attribute( $name )->value( );
			if ( ! is_null( $value ) ) {
				$attributes[ $name ] = $value;
			}
		}
		return serialize( $attributes );
	}

	public function by_fields( $fields ) {
		$datas = $this->datas_by_fields( $fields );
		if ( 
			! isset( $datas[ 0 ][ 'type' ] ) ||
			! $this->is_a( $datas[ 0 ][ 'type' ] )
		) {
			throw new \Exception\Resource_Not_Found( 'Can retrieve model data' );
		}
		return ( new \Model )
			->by_type( $datas[ 0 ][ 'type' ] )
			->by_data( $datas[ 0 ] );
	}

	protected function get_list_by_data( $datas ) {
		$entities = array( );
		foreach ( $datas as $data ) {
			if ( isset( $data[ 'type' ] ) && $this->is_a( $data[ 'type' ] ) ) {
				$entity = ( new \Model )->by_type( $data[ 'type' ] );
				$entity->init_by_data( $data );
				$entities[ $entity->id ] = $entity;
			}
		}
		return new \Object_List( $entities );
	}

	protected function has_data_changed( $current_data ) {
		$loaded_data = $this->loaded_data;
		foreach ( $current_data as $field_name => $field__value ) {
			if ( \Supersoniq\ends_with( $field_name, '_version' ) ) {
				unset( $current_data[ $field_name ] );
				unset( $loaded_data[ $field_name ] );
			}
		}
		return ( ! ( $current_data == $loaded_data ) );
	}
}

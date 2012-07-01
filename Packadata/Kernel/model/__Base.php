<?php

namespace Supersoniq\Packadata\Kernel\Model;

abstract class __Base extends \Database_Table {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public static $autoload_create_child = 'Model\\__Base';
	public $type;
	public $type_version = 1;
	protected $_attributes = array( );
	public $attributes_version = 0;
	public $is_versioned = FALSE;


	/*************************************************************************
	  GETTER & SETTER             
	 *************************************************************************/
	public function __get( $name ) {
		return $this->get( $name );
	}
	public function get( $name ) {
		if ( ! isset( $this->_attributes[ $name ] ) ) {
			return NULL;
		}
		return $this->_attributes[ $name ];
	}
	public function __set( $name, $value ) {
		return $this->set( $name, $value );
	}
	public function set( $name, $value ) {
		$this->_attributes[ $name ] = $value;
	}


	/*************************************************************************
	  ATTRIBUTES DEFINITION METHODS
	 *************************************************************************/
	public function get_attribute_fields( ) {
		return array_keys( $this->_attributes );
	}


	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( ) {
		parent::__construct( );
		$this->type = \String::substr_after_last( get_class( $this ), '\\' );
		$this->_attributes = array( );
		$this->_database->table_fields = array( 'id', 'type', 'type_version', 'attributes', 'attributes_version' );
		$this->_database->table_name = 'models';
	}

	
	/*************************************************************************
	  PUBLIC LIST METHODS
	 *************************************************************************/
	public function all( ) {
		return parent::list_by_fields( array( 'type' => $this->type ) );
	}
	public function get_archives( ) {
		$archive = new \Model_Archive( );
		return $archive->get_object_history( $this->id, array( 'type' => $this->type ) );
	}
	
	
	/*************************************************************************
	 PUBLIC DATABASE REQUEST
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
		$this->_attributes = unserialize( $data[ 'attributes' ] );
		return $data[ 'attributes' ];
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
		return serialize( $this->_attributes );
	}
	protected function has_data_changed( $current_data ) {
		$loaded_data = $this->loaded_data;
		foreach ( $current_data as $field_name => $field__value ) {
			if ( \String::ends_with( $field_name, '_version' ) ) {
				unset( $current_data[ $field_name ] );
				unset( $loaded_data[ $field_name ] );
			}
		}
		return ( ! ( $current_data == $loaded_data ) );
	}
}

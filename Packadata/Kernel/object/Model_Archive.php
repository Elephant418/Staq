<?php

namespace Supersoniq\Packadata\Kernel\Object;

abstract class Model_Archive extends \Database_Table {


	/*************************************************************************
	 ATTRIBUTES
	*************************************************************************/
	public $model_id;
	public $model_type;
	public $model_type_version;
	public $model_attributes;
	public $model_attributes_version;
	public $ip_version;
	public $date_version;


	/*************************************************************************
	 CONSTRUCTOR
	*************************************************************************/
	public function __construct( ) {
		parent::__construct( );
		$this->_database->table_fields = array(
			'id',
			'model_id',
			'model_type',
			'model_type_version',
			'model_attributes',
			'model_attributes_version',
			'ip_version',
			'date_version'
		);
		$this->_database->table_name = 'model_archives';
	}


	/*************************************************************************
	 GETTER & SETTER
	*************************************************************************/
	/*
	 * Gets the previous versions of an object
	 * @param $id the id of the concerned object
	 * @param $versions the specific model_type and/or attributes version to search for
	 * @return the array of objects found in the archives
	 */
	public function get_object_history( $id, $versions = NULL ) {
		$fields = array( 'model_id' => $id );
		if ( isset( $versions[ 'type' ] ) ) {
			$fields[ 'model_type' ] = $versions[ 'type' ];
		}
		if ( isset( $versions[ 'attributes' ] ) ) {
			$fields[ 'model_attributes_version' ] = $versions[ 'attributes' ];
		}
		return parent::list_by_fields( $fields );
	}
	/*
	 * Checks whether there is a current version of an item and gets it if there is one
	 * @param $id the id of the object to get
	 * @return the current version of the object if there is one
	 */
	public function current_version( $id ) {
		$model_type = array_values( $this->get_object_history( $id ) );
		$model = $model_type[0]->model_type;
		$model_create = '\Model\\' . $model;
		$search = new $model_create;
		$result = $search->init_by_id( $id );
		
		$versions =  array( "attributes" => $search->attributes_version );
		if ( $result ) {
			$result =  array_values( $this->get_object_history( $id, $versions ) );
			$result = $result[0];
		}
		return $result;
	}
	/*
	 * Gets the last version of an object
	 * @param $id the id of the concerned object
	 * @return the current version of the object if there is one, else the last archived version
	 */
	public function last_version( $id ) {
		if ( $this->current_version( $id ) ) {
			return ( $this->current_version( $id ) );
		} else {
			$versions = $this->get_object_history( $id );
			$max_version = 0;
			$max = NULL;
			foreach( $versions as $version ) {
				if ( $version->model_attributes_version > $max_version) {
				$max = $version;
				$max_version = $version->model_attributes_version;
				}
			}
			return $max;
		}
	}
	/*
	 * Gets the previous version of a versioned object
	 * @param $id the id of the concerned object
	 * @param $version the version of the concerned object
	 * @return the previous version of the object if there is one, else the last archived version
	 */
	public function previous_version( $id, $version ) {
		if ( $version >= 1 ) {
			$version --;
			return $this->get_object_history( $id, array( 'attributes' => $version ) );
		}
		return FALSE;
	}


	/*************************************************************************
	 INIT METHODS
	*************************************************************************/
	public function init_by_model( $model ) {
		$this->model_id = $model->id;
		$this->model_type = $model->type;
		$this->model_type_version = $model->type_version;
		$this->model_attributes = $model->table_attributes_value( );
		$this->model_attributes_version = $model->attributes_version;
		$this->ip_version = $_SERVER[ 'REMOTE_ADDR' ];
		$this->date_version = date( 'Y-m-d H:i:s' );
	}


	/*************************************************************************
	 EXTENDED METHODS
	*************************************************************************/
	protected function init_by_data( $data ) {
		$this->model_id = $data[ 'model_id' ];
		$this->model_type = $data[ 'model_type' ];
		$this->model_type_version = $data[ 'model_type_version' ];		
		if ( isset( $data[ 'model_attributes' ] ) ) {
			$data[ 'model_attributes' ] = $this->init_attributes_by_data( $data );
		}
		$this->model_attributes_version = $data[ 'model_attributes_version' ];
		$this->ip_version = $data[ 'ip_version' ];
		$this->date_version = $data[ 'date_version' ];
		return parent::init_by_data( $data );
	}
	protected function init_attributes_by_data( $data ) {
		$this->model_attributes = unserialize( $data[ 'model_attributes' ] );
		return $data[ 'model_attributes' ];
	}
	protected function table_fields_value( $field_name, $field_value = NULL ) {
		if ( $field_name != 'id' ) {
			return $this->$field_name;
		}
		return parent::table_fields_value( $field_name );
	}


	/*************************************************************************
	 UTILS
	*************************************************************************/
	/*
	 * Gets all the archives in the database
	 * @return $results an array containing all the archives
	 */
	public function all( ) {
		$all = parent::list_by_fields( array ( 'model_attributes' => array ( '>', '0' ) ) );
		$results = array( );
		foreach ( $all as $result ) {
			$result->init_by_data( $result->loaded_data );
			$results[] = $result;
		}
		return $results;
	}
}

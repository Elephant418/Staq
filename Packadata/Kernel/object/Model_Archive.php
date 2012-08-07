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
	 * Gets the model from the archive
	 */
	public function get_model( ) {
		return ( new \Model )->by_type( $this->model_attributes[ 'type' ] )->by_archive( $this );
	}
	/*
	 * Gets the previous versions of an object
	 * @param $id the id of the concerned object
	 * @param $type the specific type of model to search for
	 * @return the array of objects found in the archives
	 */
	public function get_model_history( $id, $type ) {
		$fields = array( 'model_id' => $id );
		$fields[ 'model_type' ] = $type;
		return parent::list_by_fields( $fields );
	}
	/*
	 * Gets a specific version of an object
	 * @param $id the id of the object to get
	 * @param $type the type of model of the object to get
	 * @param array $versions the array containing the versions (model or attributes) to get
	 * @return the object or the array of objects found in the archives
	 */
	public function get_model_version( $id, $type, $versions ) {
		$fields = array( 'model_id' => $id );
		$fields[ 'model_type' ] = $type;
		if ( isset( $versions[ 'attributes' ] ) ) {
			$fields[ 'model_attributes_version' ] = $versions[ 'attributes' ];
		}
		$results = parent::list_by_fields( $fields );
		if ( sizeof( $results ) == 1 ) {
			$result = array_values( $results->to_array( ) );
			return $result[ 0 ];
		} else {
			return $results;
		}
	}
	/*
	 * Checks whether there is a current version of an item and gets it if there is one
	 * @param $id the id of the object to get
	 * @param $type the type of the object
	 * @return the current version of the object if there is one, else return FALSE
	 */
	public function current_version( $id, $type ) {
		$model_create = '\__Auto\Model\\' . $type;
		$search = new $model_create;
		$result = $search->init_by_id( $id );
		
		$versions =  array( "attributes" => $search->attributes_version );
		if ( $result ) {
			$result = $this->get_model_version( $id, $type, $versions );
			return $result;
		}
		return FALSE;
	}
	/*
	 * Gets the last version of an object
	 * @param $id the id of the concerned object
	 * @param $type the type of the object
	 * @return the current version of the object if there is one, else the last archived version
	 */
	public function last_version( $id, $type ) {
		if ( $this->current_version( $id, $type ) ) {
			return ( $this->current_version( $id, $type ) );
		} else {
			$versions = $this->get_model_history( $id, $type );
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


	/*************************************************************************
	 INIT METHODS
	*************************************************************************/
	public function init_by_model( $model ) {
		$this->model_id = $model->id;
		$this->model_type = $model->type;
		$this->model_type_version = $model->type_version;
		$this->model_attributes = $model->get_current_data( );
		$this->model_attributes_version = $model->attributes_version;
		$this->ip_version = $_SERVER[ 'REMOTE_ADDR' ];
		$this->date_version = date( 'Y-m-d H:i:s' );
	}


	/*************************************************************************
	 EXTENDED METHODS
	*************************************************************************/
	protected function init_by_data( $data ) {		
		if ( isset( $data[ 'model_attributes' ] ) && ! is_array( $data[ 'model_attributes' ] ) ) {
			$data[ 'model_attributes' ] = unserialize( $data[ 'model_attributes' ] );
		}
		foreach ( $data as $field_name => $field_value ) {
			$this->$field_name = $field_value;
		}
		return parent::init_by_data( $data );
	}
	protected function table_fields_value( $field_name, $field_value = NULL ) {
		if ( $field_name == 'model_attributes' ) {
			return serialize( $this->model_attributes );
		} else if ( $field_name != 'id' ) {
			return $this->$field_name;
		}
		return parent::table_fields_value( $field_name );
	}


	/*************************************************************************
	 UTILS
	*************************************************************************/
	/*
	 * Gets all the archives in the database
	 * @param $type the type of the objects to get
	 * @return $results an array containing all the archives
	 */
	public function all( $type=NULL ) {
		if ( isset( $type ) ) {
			$all = parent::list_by_fields( array ( 'model_type' => $type ) );
		} else {
			$all = parent::list_by_fields( array ( 'model_attributes' => array ( '>', '0' ) ) );
		}
		$results = array( );
		foreach ( $all as $result ) {
			$result->init_by_data( $result->loaded_data );
			$results[] = $result;
		}
		return $results;
	}
}

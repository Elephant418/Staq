<?php

namespace Supersoniq\Packadata\Kernel\Object;

abstract class Model_Index extends \Database_Table {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	const INDEXED = 1;
	const UNIQUE = 2;
	public $value;
	public $type;
	public $model_id;
	public $model_type;


	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( ) {
		parent::__construct( );
		$this->_database->table_fields = array( 'id', 'model_type', 'model_id', 'type', 'value' );
		$this->_database->table_name = 'indexs';
	}
	public function init( $model, $type ) {
		$this->model_type = $model->type;
		$this->type = $type;
	}

	
	/*************************************************************************
	  PUBLIC ACCESS METHODS
	 *************************************************************************/
	public function all( ) {
		$fields = array( 
			'type' => $this->type, 
			'model_type' => $this->model_type,
		);
		if ( ! is_null( $this->model_id ) ) {
			$fields[ 'model_id' ] = $this->model_id;
		}
		return $this->list_by_fields( $fields );
	}
	public function model_id_by_value( $model_type, $type, $value ) {
		$datas = $this->datas_by_fields( array(
			'model_type' => $model_type,
			'type' => $type,
			'value' => $value,
		) );
		if ( count( $datas ) > 0 && isset( $datas[ 0 ][ 'model_id' ] ) ) {
			return $datas[ 0 ][ 'model_id' ];
		}
		return FALSE;
	}


	/*************************************************************************
	  MODEL EVENT METHODS             
	 *************************************************************************/
	public function is_uniq( $model ) {
		$this->init_by_model( $model );
		$fields = array(
			'model_type' => $this->model_type,
			'type' => $this->type,
			'value' => $this->value,
		);
		if ( ! is_null( $this->model_id ) ) {
			$fields[ 'model_id' ] = array( '<>', $this->model_id );
		}
		$datas = $this->datas_by_fields( $fields );
		return ( count( $datas ) == 0 );
	}
	public function model_saved( $model ) {
		$this->init_by_model( $model );
		$this->save( );
	}
	public function model_deleted( $model ) {
		$this->init_by_model( $model );
		$this->delete( );
	}

	
	/*************************************************************************
	  PRIVATE METHODS
	 *************************************************************************/
	private function init_by_model( $model ) {
		$this->model_type = $model->type;
		$this->model_id = $model->id;
		$this->by_fields( [ 
			'model_type' => $this->model_type,
			'model_id' => $this->model_id,
			'type' => $this->type,
		] );
		$this->value = $model->{$this->type};
		return $index;
	}

	
	/*************************************************************************
	  EXTENDED METHODS
	 *************************************************************************/
	protected function init_by_data( $data ) {
		if ( isset( $data[ 'type' ] ) && $data[ 'type' ] != $this->type ) {
			throw new \Exception( 'Try to initialize a "' . $this->type . '" index with "' . $data[ 'type' ] . '" data.' );
		}
		if ( isset( $data[ 'model_type' ] ) && $data[ 'model_type' ] != $this->model_type ) {
			throw new \Exception( 'Try to initialize an "' . $this->model_type . ':' . $this->type . '" index from a "' . $data[ 'model_type' ] . '" model.' );
		}
		if ( ! is_null( $this->model_id ) && isset( $data[ 'model_id' ] ) && $data[ 'model_id' ] != $this->model_id ) {
			throw new \Exception( 'Try to initialize an "' . $this->model_type . ':' . $this->model_id . ':' . $this->type . '" index from the "' . $this->model_type . ':' . $data[ 'model_id' ] . '" model.' );
		}
		if ( isset( $data[ 'model_id' ] ) ) {
			$this->model_id = $data[ 'model_id' ];
		}
		if ( isset( $data[ 'value' ] ) ) {
			$this->value = $data[ 'value' ];
		}
		return parent::init_by_data( $data );
	}
	protected function table_fields_value( $field_name, $field_value = NULL ) {
		if ( $field_name == 'type' ) {
			return $this->type;
		} else if ( $field_name == 'model_id' ) {
			return $this->model_id;
		} else if ( $field_name == 'model_type' ) {
			return $this->model_type;
		} else if ( $field_name == 'value' ) {
			return $this->value;
		}
		return parent::table_fields_value( $field_name ); 
	}
	protected function new_entity( ) {
		$class = get_class( $this );
		$relation = new $class( $this->is_reverse( ) );
		$relation->type = $this->type;
		$relation->model_type = $this->model_type;
		$relation->model_id = $this->model_id;
		return $relation;
	}
}

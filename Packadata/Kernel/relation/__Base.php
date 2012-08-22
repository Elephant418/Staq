<?php

namespace Supersoniq\Packadata\Kernel\Relation;

abstract class __Base extends \Database_Table {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	const REVERSED = TRUE;
	protected $model;
	protected $related_model;
	public $related_model_type;
	protected $type;
	protected $_number;
	protected $_related_number;


	/*************************************************************************
	  GETTER & SETTER             
	 *************************************************************************/
	public function get( ) {
		return $this->related_model;
	}
	public function set( $related_model ) {
		if ( ! is_null( $this->related_model_type ) ) {
			if ( 
				$related_model->type != $this->related_model_type && 
				! \Supersoniq\starts_with( $related_model->type, $this->related_model_type . '\\' )
			) {
				throw new \Exception\Wrong_Model_Type_For_Relation( 'Model of type "' . $this->related_model_type . '" expected for the "' . $this->type . '" Relation, but "' . $related_model->type . '" found' );
			}
		}
		$this->related_model = $related_model;
	}


	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $related_model_type = NULL, $is_reverse = FALSE ) {
		parent::__construct( );
		$this->set_is_reverse( $is_reverse );
		$this->related_model_type = $related_model_type;
		$this->type = \Supersoniq\substr_after_last( get_class( $this ), '\\' );
		$this->_database->table_fields = array( 'id', 'model_id_1', 'model_type_1', 'model_id_2', 'model_type_2', 'type' );
		$this->_database->table_name = 'relations';
	}
	private function set_is_reverse( $is_reverse ) {
		$number = ( $is_reverse ) ? 2 : 1;
		$this->_number = $number;
		$this->_related_number = 3 - $number;
	}
	private function is_reverse( ) {
		return ( $this->_number == 2 );
	}


	/*************************************************************************
	  INITIALIZATION
	 *************************************************************************/
	public function set_model( $model ) {
		$this->model = $model;
		return $this;
	}

	
	/*************************************************************************
	  PUBLIC LIST METHODS
	 *************************************************************************/
	public function all( ) {
		if ( ! is_object( $this->model ) ) {
			return new \Object_List;
		}
		return $this->list_by_fields( array( 
			'type' => $this->type, 
			'model_id_' . $this->_number => $this->model->id,
			'model_type_' . $this->_number => $this->model->type,
		) );
	}
	public function delete_all( ) {
		if ( is_object( $this->model ) ) {
			return $this->delete_by_fields( array( 
				'type' => $this->type, 
				'model_id_' . $this->_number => $this->model->id,
				'model_type_' . $this->_number => $this->model->type,
			) );
		}
	}
	public function model_deleted_all( ) {
		if ( is_object( $this->model ) ) {
			return $this->delete_by_fields( array( 
				'type' => $this->type, 
				'model_type_' . $this->_number => $this->model->type,
			) );
		}
	}

	
	/*************************************************************************
	  EXTENDED METHODS
	 *************************************************************************/
	protected function init_by_data( $data ) {
		if ( isset( $data[ 'type' ] ) && $data[ 'type' ] != $this->type ) {
			throw new \Exception( 'Try to initialize a "' . $this->type . '" relation with "' . $data[ 'type' ] . '" data.' );
		}
		if ( 
			( isset( $data[ 'model_id_'   . $this->_number ] ) && $data[ 'model_id_'   . $this->_number ] != $this->model->id   ) ||
			( isset( $data[ 'model_type_' . $this->_number ] ) && $data[ 'model_type_' . $this->_number ] != $this->model->type )
		) {
			throw new \Exception( 'Try to initialize a relation from "' . $this->model->type . ':' . $this->model->id . '" with "' . $data[ 'model_type_' . $this->_number ] . ':' . $data[ 'model_id_'   . $this->_number ] . '" data.' );
		}
		if ( isset( $data[ 'model_id_' . $this->_related_number ] ) && isset( $data[ 'model_type_' . $this->_related_number ] ) ) {
			$this->related_model = ( new \Model )
				->by_type( $data[ 'model_type_' . $this->_related_number ] )
				->by_id( $data[ 'model_id_' . $this->_related_number ] );
		}
		return parent::init_by_data( $data );
	}
	protected function table_fields_value( $field_name, $field_value = NULL ) {
		if ( $field_name == 'type' ) {
			return $this->type;
		} else if ( $field_name == 'model_id_' . $this->_number ) {
			return $this->model->id;
		} else if ( $field_name == 'model_type_' . $this->_number ) {
			return $this->model->type;
		} else if ( $field_name == 'model_id_' . $this->_related_number ) {
			return $this->related_model->id;
		} else if ( $field_name == 'model_type_' . $this->_related_number ) {
			return $this->related_model->type;
		}
		return parent::table_fields_value( $field_name ); 
	}
	protected function new_entity( ) {
		return ( new $this( $this->related_model_type, $this->is_reverse( ) ) )
			->set_model( $this->model );
	}
}

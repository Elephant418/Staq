<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Database;

class Entity implements \Stack\IEntity {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public static $setting = [
		'database.id_field' => 'id',
		'database.fields'   => [ 'id' ]
	];
	protected $settings;
	protected $name;
	protected $table;
	protected $id_field;
	protected $fields;



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( ) {
		$this->settings = ( new \Stack\Setting )->parse( $this );
		$this->name     = strtolower( \Staq\Util::getStackSubQuery( $this, '_' ) );
		$this->table    = $this->settings->get( 'database.table', $this->name );
		$this->id_field = $this->settings[ 'database.id_field' ];
		$this->fields   = $this->settings->getAsArray( 'database.fields' );
	}


	/*************************************************************************
	  FETCHING METHODS          
	 *************************************************************************/
	public function extract_id( &$data ) {
		$id = NULL;
		if ( isset( $data[ $this->id_field ] ) ) {
			$id = $data[ $this->id_field ];
			unset( $data[ $this->id_field ] );
		}
		return $id;
	}

	public function get_data_by_id( $id ) {
		return $this->get_data_by_fields( [ $this->id_field => $id ] );
	}

	public function get_data_by_fields( $where = [ ] ) {
		$datas = $this->get_datas_by_fields( $where, 1 );
		if ( isset( $datas[ 0 ] ) ) {
			return $datas[ 0 ];
		}
		return FALSE;
	}

	public function get_datas_by_fields( $where = [ ], $limit = NULL, $order = NULL ) {
		$parameters = [ ];
		$sql = $this->get_base_select( ) . $this->get_clause_by_fields( $where, $parameters, $limit, $order );
		$request = new Request( $sql );
		return $request->execute( $parameters );
	}

	public function delete_by_fields( $where ) {
		$parameters = [ ];
		$sql = 'DELETE FROM ' . $this->table . $this->get_clause_by_fields( $where, $parameters );
		$request = new Request( $sql );
		return $request->execute( $parameters );
	}


	/*************************************************************************
	  PUBLIC DATABASE REQUEST
	 *************************************************************************/
	public function delete( $model = NULL ) {
		$sql = 'DELETE FROM ' . $this->table;
		$parameters = [ ];
		if ( ! is_null( $model ) ) {
			if ( ! $model->exists( ) ) {
				return TRUE;
			}
			$sql .= ' WHERE ' . $this->id_field . '=:id';
			$parameters[ ':id' ] = $model->id;
		}
		$request = new Request( $sql );
		return $request->execute_one( $parameters );
	}

	public function save( $model ) {
		if ( $model->exists( ) ) {
			$sql = 'UPDATE ' . $this->table
			. ' SET ' . $this->get_set_request( $model )
			. ' WHERE `' . $this->id_field . '` = :' . $this->id_field . ' ;';
			$request = new Request( $sql );
			$request->execute_one( $this->get_bind_params( $model ) );
			return $model->id;
		} else {
			$sql = 'INSERT INTO ' . $this->table
			. ' (`' . implode( '`, `', $this->fields ) . '`) VALUES'
			. ' (:' . implode( ', :', $this->fields ) . ');';
			$request = new Request( $sql );
			$request->execute_one( $this->get_bind_params( $model ) );
			return $request->get_last_insert_id( );
		}
	}

	
	/*************************************************************************
	  PRIVATE METHODS
	 *************************************************************************/
	protected function get_base_select( ) {
		return 'SELECT ' . $this->get_base_selector( ) . ' FROM ' . $this->get_base_table( );
	}

	protected function get_base_selector( ) {
		$fields = array_map( function( $field ) {
			return $this->table . '.' . $field;
		}, $this->fields);
		return implode( ', ', $fields );
	}

	protected function get_base_table( ) {
		return $this->table;
	}

	protected function get_clause_by_fields( $request, &$parameters, $limit = NULL, $order = NULL ) {
		$where = [ ];
		if ( is_array( $request ) ) {
			foreach ( $request as $field_name => $field_value ) {
				if ( is_numeric( $field_name ) ) {
					if ( is_string( $field_value ) ) {
						$where[ ] = $field_value;
					} else if (
						is_array( $field_value ) &&
						isset( $field_value[ 0 ] ) &&
						isset( $field_value[ 1 ] ) &&
						isset( $field_value[ 2 ] ) 
					) {
						$where[ ] = $this->get_clause_condition( $parameters, $field_value[ 0 ], $field_value[ 1 ], $field_value[ 2 ] );
					}
				} else {
					if ( ! \UString::has( $field_name, '.' ) ) {
						$field_name = $this->table . '.' . $field_name;
					}
					$where[ ] = $this->get_clause_condition( $parameters, $field_name, '=', $field_value );
				}
			}
		}
		$sql = '';
		if ( ! empty( $where ) ) {
			$sql .= ' WHERE ' . implode ( ' AND ', $where );
		}
		if ( ! is_null( $order ) ) {
			$sql .= ' ORDER BY ' . $order;
		}
		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . $limit;
		}
		return $sql . ';';
	}

	protected function get_clause_condition( &$parameters, $field_name, $operator, $field_value ) {
		$condition = NULL;	
		$parameter_name = 'key' . count( $parameters );
		if ( is_array( $field_value ) ) {
			$condition_parameters = [ ];
			foreach( $field_value as $key => $value ) {
				$condition_parameters[ ':' . 'key_' . ( count( $parameters ) + $key ) ] = $value;
			}
			$condition = implode( ', ', array_keys( $condition_parameters ) );
			$condition = $field_name . ' IN ( ' . $condition . ' )';
			$parameters = array_merge( $parameters, $condition_parameters );
		} else {
			$condition = $field_name . ' ' . $operator . ' :' . $parameter_name;
			$parameters[ ':' . $parameter_name ] = $field_value;
		}
		return $condition;
	}

	protected function get_set_request( ) {
		$request = [ ];
		foreach ( $this->fields as $field_name ) {
			if ( $field_name != $this->id_field ) {
				$request[ ] = '`' . $field_name . '` = :' . $field_name;
			}
		}
		return implode( ', ', $request );
	}

	protected function get_bind_params( $model ) {
		$data = $this->get_current_data( $model );
		$bind_params = [ ];
		foreach ( $this->fields as $field_name ) {
			$field_value = NULL;
			if ( isset( $data[ $field_name ] ) ) {
				$field_value = $data[ $field_name ];
			}
			$bind_params[  $field_name ] = $field_value;
		}
		return $bind_params;
	}

	protected function get_current_data( $model ) {
		$data = $model->extract_seeds( );
		$data[ $this->id_field ] = $model->id;
		return $data;
		// DO: Manage serializes extra fields here ;)
	}
}

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
		$this->name     = strtolower( \Staq\Util::stack_sub_query( $this, '_' ) );
		$this->table    = $this->settings->get( 'database.table', $this->name );
		$this->id_field = $this->settings[ 'database.id_field' ];
		$this->fields   = $this->settings->get_as_array( 'database.fields' );
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

	public function get_data_by_fields( $fields = [ ] ) {
		$datas = $this->get_datas_by_fields( $fields, 1 );
		if ( isset( $datas[ 0 ] ) ) {
			return $datas[ 0 ];
		}
		return FALSE;
	}

	public function get_datas_by_fields( $fields = [ ], $limit = NULL ) {
		$request = [ 'where' => $fields ];
		if ( ! is_null( $limit ) ) {
			$request[ 'limit' ] = $limit;
		}
		$parameters = [ ];
		$sql = 'SELECT * FROM ' . $this->table . $this->get_clause_by_fields( $request, $parameters );
		$request = new Request( $sql );
		return $request->execute( $parameters );
	}

	public function delete_by_fields( $fields ) {
		$request = [ 'where' => $fields ];
		$parameters = [ ];
		$sql = 'DELETE FROM ' . $this->table . $this->get_clause_by_fields( $request, $parameters );
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
	protected function get_clause_by_fields( $request, &$parameters ) {
		$where = [ ];
		$limit = NULL;
		if ( isset( $request[ 'limit' ] ) ) {
			$limit = $request[ 'limit' ];
		}
		if ( isset( $request[ 'where' ] ) && is_array( $request[ 'where' ] ) ) {
			foreach ( $request[ 'where' ] as $field_name => $field_value ) {
				if ( is_numeric( $field_name ) ) {
					if ( 
						is_array( $field_value ) &&
						isset( $field_value[ 0 ] ) &&
						isset( $field_value[ 1 ] ) &&
						isset( $field_value[ 2 ] ) 
					) {
						$where[ ] = $field_value[ 0 ] . $field_value[ 1 ] . ':' . $field_value[ 0 ];
						$parameters[ ':' . $field_value[ 0 ] ] = $field_value[ 2 ];
					}
				} else if ( is_array( $field_value ) ) {
					$clause = $field_name . ' IN ( ';
					$clause_parameters = [ ];
					foreach( $field_value as $key => $value ) {
						$clause_parameters[ ':' . $field_name . '_' .$key ] = $value;
					}
					$clause .= implode( ', ', array_keys( $clause_parameters ) ) . ')';
					$parameters = array_merge( $parameters, $clause_parameters );
					$where[ ] = $clause;
				} else {
					$where[ ] = $field_name . '=:' . $field_name;
					$parameters[ ':' . $field_name ] = $field_value;
				}
			}
		}
		$sql = '';
		if ( ! empty( $where ) ) {
			$sql .= ' WHERE ' . implode ( ' AND ', $where );
		}
		if ( ! is_null( $limit ) ) {
			$sql .= ' LIMIT ' . $limit;
		}
		return $sql . ';';
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

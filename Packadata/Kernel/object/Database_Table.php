<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Object;

abstract class Database_Table {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public $id;
	protected $_database;
	protected $loaded_data = array( );


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
		$this->_database = new \Database_Table_Definition( );
	}


	/*************************************************************************
	  INITIALIZATION          
	 *************************************************************************/
	public function by_id( $id ) {
		return $this->by_fields( array( $this->_database->id_field => $id ) );
	}

	public function by_fields( $fields ) {
		$datas = $this->datas_by_fields( $fields );
		if ( isset( $datas[ 0 ] ) ) {
			return $this->by_data( $datas[ 0 ] );
		}
		return $this;
	}

	public function by_data( $fields ) {
		$this->init_by_data( $fields );
		return $this;
	}

	public function list_by_fields( $fields = [ ] ) {
		$datas = $this->datas_by_fields( $fields );
		return $this->get_list_by_data( $datas );
	}

	protected function delete_by_fields( $fields ) {
		$parameters = [ ];
		$sql = 'DELETE FROM ' . $this->_database->table_name . $this->where_clause_by_fields( $fields, $parameters );
		$request = new Database_Request( $sql );
		return $request->execute( $parameters );
	}


	/*************************************************************************
	  PUBLIC DATABASE REQUEST
	 *************************************************************************/
	public function delete( ) {
		if ( $this->exists( ) ) {
			$sql = 'DELETE FROM ' . $this->_database->table_name . ' WHERE ' . $this->_database->id_field . '=:id;';
			$request = new Database_Request( $sql );
			$request->execute_one( array( ':id' => $this->id ) );
			$this->deleted_handler( );
		}
	}

	public function save( $force_insert = FALSE ) {
		$current_data = $this->get_current_data( );
		if ( $this->has_data_changed( $current_data ) ) {
			if ( $this->exists( ) && ! $force_insert ) {
				$sql = 'UPDATE ' . $this->_database->table_name
				. ' SET ' . $this->get_set_request( )
				. ' WHERE `' . $this->_database->id_field.'` = :' . $this->_database->id_field . ' ;';
				$request = new Database_Request( $sql );
				$request->execute_one( $this->bind_params( $current_data ) );
			} else {
				$sql = 'INSERT INTO ' . $this->_database->table_name
				. ' (`' . implode( '`, `', $this->_database->table_fields ) . '`) VALUES'
				. ' (:' . implode( ', :', $this->_database->table_fields ) . ');';
				$request = new Database_Request( $sql );
				$request->execute_one( $this->bind_params( $current_data ) );
				$id = $request->last_insert_id( );
				if ( $id !== FALSE ) {
					$this->id = $id;
				}
			}
			$this->loaded_data = $current_data;
			$this->saved_handler( );
		}		
		return TRUE;
	}

	public function get_current_data( ) {
		$data = array( );
		foreach ( $this->_database->table_fields as $field_name ) {
			$data[ $field_name ] = $this->table_fields_value( $field_name );
		}
		return $data;
	}

	
	/*************************************************************************
	  HANDLERS
	 *************************************************************************/
	protected function saved_handler( ) {
	}

	protected function deleted_handler( ) {
		$this->id = NULL;
		$this->loaded_data = array( );
	}

	
	/*************************************************************************
	  METHODS TO EXTEND
	 *************************************************************************/
	protected function init_by_data( $data ) {
		if ( isset( $data[ $this->_database->id_field ] ) ) {
			$this->id = $data[ $this->_database->id_field ];
		}
		$this->loaded_data = $data;
	}

	protected function table_fields_value( $field_name, $field_value = NULL ) {
		if ( $field_name == $this->_database->id_field ) {
			if ( func_num_args( ) == 1 ) {
				return $this->id;
			}
			$this->id = $field_value;
		}
		throw new \Exception( 'Unknow table field "' . $field_name . '"' ); 
	}

	protected function has_data_changed( $current_data ) {
		return ( ! ( $current_data == $this->loaded_data ) );
	}

	protected function new_entity( ) {
		$class = get_class( $this );
		return new $class( );
	}

	
	/*************************************************************************
	  PRIVATE METHODS
	 *************************************************************************/
	protected function datas_by_fields( $fields ) {
		$parameters = [ ];
		$sql = 'SELECT * FROM ' . $this->_database->table_name . $this->where_clause_by_fields( $fields, $parameters );
		$request = new Database_Request( $sql );
		return $request->execute( $parameters );
	}

	protected function where_clause_by_fields( $fields, &$parameters ) {
		$where = [ ];
		foreach ( $fields as $fields_name => $field_value ) {
			if ( is_array( $field_value ) ) {
				if ( isset( $field_value[ 'where' ] ) && isset( $field_value[ 'parameters' ] ) ) {
					$where[ ] = '( ' . $field_value[ 'where' ] . ' )';
					$parameters = array_merge( $parameters, $field_value[ 'parameters' ] );
				} else if ( isset( $field_value[ 0 ] ) && isset( $field_value[ 1 ] ) ) {
					$where[ ] = $fields_name . $field_value[ 0 ] . ':' . $fields_name;
					$parameters[ ':' . $fields_name ] = $field_value[ 1 ];
				}
			} else {
				$where[ ] = $fields_name . '=:' . $fields_name;
				$parameters[ ':' . $fields_name ] = $field_value;
			}
		}
		$sql = '';
		if ( ! empty( $where ) ) {
			$sql .= ' WHERE ' . implode ( ' AND ', $where ) . ';';
		}
		return $sql;
	}

	protected function get_list_by_data( $datas ) {
		$entities = [ ];
		foreach ( $datas as $data ) {
			$entity = $this->new_entity( );
			$entity->init_by_data( $data );
			$entities[ $entity->id ] = $entity;
		}
		return new \Object_List( $entities );
	}

	private function get_set_request( ) {
		$request = '';
		foreach ( $this->_database->table_fields as $field_name ) {
			$request .= '`' . $field_name . '` = :' . $field_name . ', ';
		}
		return substr( $request, 0, -2 );
	}

	private function bind_params( $fields ) {
		$bind_params = array( );
		foreach ( $fields as $field_name => $field_value) {
			$bind_params[ ':' . $field_name ] = $field_value;
		}
		return $bind_params;
	}
}

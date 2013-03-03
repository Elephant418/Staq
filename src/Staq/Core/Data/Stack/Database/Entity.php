<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Database;

class Entity implements \Stack\IEntity {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public static $setting = [
		'database.idField' => 'id',
		'database.fields'   => [ 'id' ]
	];
	protected $settings;
	protected $name;
	protected $table;
	protected $idField;
	protected $fields;



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( ) {
		$this->settings = ( new \Stack\Setting )->parse( $this );
		$this->name     = strtolower( \Staq\Util::getStackSubQuery( $this, '_' ) );
		$this->table    = $this->settings->get( 'database.table', $this->name );
		$this->idField  = $this->settings[ 'database.idField' ];
		$this->fields   = $this->settings->getAsArray( 'database.fields' );
	}


	/*************************************************************************
	  FETCHING METHODS          
	 *************************************************************************/
	public function extractId( &$data ) {
		$id = NULL;
		if ( isset( $data[ $this->idField ] ) ) {
			$id = $data[ $this->idField ];
			unset( $data[ $this->idField ] );
		}
		return $id;
	}

	public function getDataById( $id ) {
		return $this->getDataByFields( [ $this->idField => $id ] );
	}

	public function getDataByFields( $where = [ ] ) {
		$datas = $this->getDatasByFields( $where, 1 );
		if ( isset( $datas[ 0 ] ) ) {
			return $datas[ 0 ];
		}
		return FALSE;
	}

	public function getDatasByFields( $where = [ ], $limit = NULL, $order = NULL ) {
		$parameters = [ ];
		$sql = $this->getBaseSelect( ) . $this->getClauseByFields( $where, $parameters, $limit, $order );
		$request = new Request( $sql );
		return $request->execute( $parameters );
	}

	public function deleteByFields( $where ) {
		$parameters = [ ];
		$sql = 'DELETE FROM ' . $this->table . $this->getClauseByFields( $where, $parameters );
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
			$sql .= ' WHERE ' . $this->idField . '=:id';
			$parameters[ ':id' ] = $model->id;
		}
		$request = new Request( $sql );
		return $request->executeOne( $parameters );
	}

	public function save( $model ) {
		if ( $model->exists( ) ) {
			$sql = 'UPDATE ' . $this->table
			. ' SET ' . $this->getSetRequest( $model )
			. ' WHERE `' . $this->idField . '` = :' . $this->idField . ' ;';
			$request = new Request( $sql );
			$request->executeOne( $this->getBindParams( $model ) );
			return $model->id;
		} else {
			$sql = 'INSERT INTO ' . $this->table
			. ' (`' . implode( '`, `', $this->fields ) . '`) VALUES'
			. ' (:' . implode( ', :', $this->fields ) . ');';
			$request = new Request( $sql );
			$request->executeOne( $this->getBindParams( $model ) );
			return $request->getLastInsertId( );
		}
	}

	
	/*************************************************************************
	  PRIVATE METHODS
	 *************************************************************************/
	protected function getBaseSelect( ) {
		return 'SELECT ' . $this->getBaseSelector( ) . ' FROM ' . $this->getBaseTable( );
	}

	protected function getBaseSelector( ) {
		$fields = array_map( function( $field ) {
			return $this->table . '.' . $field;
		}, $this->fields);
		return implode( ', ', $fields );
	}

	protected function getBaseTable( ) {
		return $this->table;
	}

	protected function getClauseByFields( $request, &$parameters, $limit = NULL, $order = NULL ) {
		$where = [ ];
		if ( is_array( $request ) ) {
			foreach ( $request as $fieldName => $fieldValue ) {
				if ( is_numeric( $fieldName ) ) {
					if ( is_string( $fieldValue ) ) {
						$where[ ] = $fieldValue;
					} else if (
						is_array( $fieldValue ) &&
						isset( $fieldValue[ 0 ] ) &&
						isset( $fieldValue[ 1 ] ) &&
						isset( $fieldValue[ 2 ] ) 
					) {
						$where[ ] = $this->getClauseCondition( $parameters, $fieldValue[ 0 ], $fieldValue[ 1 ], $fieldValue[ 2 ] );
					}
				} else {
					if ( ! \UString::has( $fieldName, '.' ) ) {
						$fieldName = $this->table . '.' . $fieldName;
					}
					$where[ ] = $this->getClauseCondition( $parameters, $fieldName, '=', $fieldValue );
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

	protected function getClauseCondition( &$parameters, $fieldName, $operator, $fieldValue ) {
		$condition = NULL;	
		$parameterName = 'key' . count( $parameters );
		if ( is_array( $fieldValue ) ) {
			$condition_parameters = [ ];
			foreach( $fieldValue as $key => $value ) {
				$condition_parameters[ ':' . 'key_' . ( count( $parameters ) + $key ) ] = $value;
			}
			$condition = implode( ', ', array_keys( $condition_parameters ) );
			$condition = $fieldName . ' IN ( ' . $condition . ' )';
			$parameters = array_merge( $parameters, $condition_parameters );
		} else {
			$condition = $fieldName . ' ' . $operator . ' :' . $parameterName;
			$parameters[ ':' . $parameterName ] = $fieldValue;
		}
		return $condition;
	}

	protected function getSetRequest( ) {
		$request = [ ];
		foreach ( $this->fields as $fieldName ) {
			if ( $fieldName != $this->idField ) {
				$request[ ] = '`' . $fieldName . '` = :' . $fieldName;
			}
		}
		return implode( ', ', $request );
	}

	protected function getBindParams( $model ) {
		$data = $this->getCurrentData( $model );
		$bind_params = [ ];
		foreach ( $this->fields as $fieldName ) {
			$fieldValue = NULL;
			if ( isset( $data[ $fieldName ] ) ) {
				$fieldValue = $data[ $fieldName ];
			}
			$bind_params[  $fieldName ] = $fieldValue;
		}
		return $bind_params;
	}

	protected function getCurrentData( $model ) {
		$data = $model->extractSeeds( );
		$data[ $this->idField ] = $model->id;
		return $data;
		// DO: Manage serializes extra fields here ;)
	}
}

<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Database;

class Request {



	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	protected $request;
	protected $PDObject;
	protected $last_insert_id = false;



	/*************************************************************************
	  GETTER                   
	 *************************************************************************/
	public function get_last_insert_id( ) {
		return $this->last_insert_id;
	}
	
	public function get_PDObject( ) {
		$this->connect( );
		return $this->PDObject;
	}
	
	public function get_request( ) {
		return $this->request;
	}



	/*************************************************************************
	  SETTER                   
	 *************************************************************************/
	public function set_PDObject( $PDObject ) {
		$this->PDObject = $PDObject;
		return $this;
	}
	
	public function set_request( $request ) {
		$this->request = $request;
		return $this;
	}



	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( $request = '' ) {
		$this->set_request( $request );
	}
	
	

	/*************************************************************************
	  PUBLIC METHODS                   
	 *************************************************************************/
	public function execute_one( $arguments = array( ) ) {
		$result = $this->execute( $arguments );
		if ( is_array( $result ) && count( $result ) > 0 ) {
			$result = $result[ 0 ];
		}
		return $result;
	}

	public function execute( $arguments = array( ) ) {

		if ( empty( $this->request ) ) {
			throw new \Stack\Exception\Database( 'The SQL request is empty.' );
		}
		
		$result = [ ];
		try {

			// Prepare the request
			$this->connect( );
			$statement = $this->PDObject->prepare( $this->request );
			foreach( $arguments as $parameter => $value ) {
				$statement->bindValue( $parameter, $value );
			}
			$result = $statement->execute( );

			// Execute the request
			if ( ! $result ) {
				throw new \Stack\Exception\Database( 'Error with the SQL request : ' . $this->request, $statement->errorInfo( ) );
			}
		
			if ( \UString::is_start_with( $this->request, [ 'SELECT', 'SHOW', 'DESCRIBE', 'EXPLAIN' ] ) ) {
				$result = $statement->fetchAll( \PDO::FETCH_ASSOC );
			} else if ( \UString::is_start_with( $this->request, "INSERT" ) ) {
				$result = TRUE;
				$id = $this->PDObject->lastInsertId( );
				if ( $id == '0' ) {
					// Error Case or a table without autoincrementation
					$id = FALSE;
					$result = FALSE;
				}
				$this->last_insert_id = $id;
			}
			
		} catch ( PDOException $exception ) {
			throw new \Stack\Exception\Database( $exception->getMessage( ) );
		}
		$this->disconnect( );
		return $result;
	}

	public function require_database( $name = NULL ) {
		if ( is_null( $name ) ) {
			$ini  = ( new \Stack\Setting )->parse( 'database' );
			$name = $ini[ 'access.name' ];
		}
		$this->connect( FALSE );
		$statement = $this->PDObject->prepare( 'CREATE DATABASE IF NOT EXISTS `' . $name . '`;' );
		$statement->execute( );
		return $this;
	}

	public function load_mysql_file( $file ) {
		$ini = ( new \Stack\Setting )->parse( 'database' );
		$system = 'mysql -u' . $ini[ 'access.user' ];
		if ( isset( $ini[ 'access.password' ] ) ) {
			$system .= ' -p' . $ini[ 'access.password' ];
		}
		$system .= ' -D ' . $ini[ 'access.name' ] . ' < ' . $file;
		system( $system );
		return $this;
	}


	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function connect( $database = TRUE ) {
		if ( empty( $this->PDObject ) ) {
			$ini  = ( new \Stack\Setting )->parse( 'database' );
			$conf = $ini[ 'access.driver' ] . ':host=' . $ini[ 'access.host' ];
			if ( $database ) {
				$conf .= ';dbname=' . $ini[ 'access.name' ];
			}
			$this->PDObject = new \PDO( $conf, $ini[ 'access.user' ], $ini[ 'access.password' ], [ \PDO::ATTR_PERSISTENT => TRUE ] );
			$this->PDObject->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
		}
	}

	protected function disconnect( ) {
		$this->PDObject = NULL;
	}
}





<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Database;

class Request {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public $request;
	protected $PDObject;
	protected $last_insert_id = false;


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( $request = '' ) {
		$this->request = $request;
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
	
	public function get_last_insert_id( ) {
		return $this->last_insert_id;
	}
	
	public function set_PDObject( $PDObject ) {
		$this->PDObject = $PDObject;
	}
	
	public function get_PDObject( ) {
		if ( empty( $this->PDObject ) ) {
			$ini = ( new \Stack\Setting )->parse( 'database' );
			$this->PDObject = new \PDO(
				$ini[ 'access.driver' ] . ':host=' . $ini[ 'access.host' ] . ';dbname=' . $ini[ 'access.name' ],
				$ini[ 'access.user' ],
				$ini[ 'access.password' ],
				[ \PDO::ATTR_PERSISTENT => TRUE ]
			);
			$this->PDObject->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
		}
		return $this->PDObject;
	}

	public function load_mysql_file( $file ) {
		$ini = ( new \Stack\Setting )->parse( 'database' );
		system( 'mysql -u' . $ini[ 'access.user' ] . ' -p' . $ini[ 'access.password' ] . ' -D ' . $ini[ 'access.name' ] . ' < ' . $file );
	}


	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function connect( ) {
		$this->get_PDObject( );
	}

	protected function disconnect( ) {
		$this->PDObject = NULL;
	}
}





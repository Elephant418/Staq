<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

class Url {



	/*************************************************************************
	  ATTRIBUTES                   
	 *************************************************************************/
	public $host;
	public $port;
	public $uri;



	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function from( $mixed ) {
		if ( is_object( $mixed ) ) {
			return $mixed;
		}
		if ( is_array( $mixed ) ) {
			return $this->from_array( $mixed );
		}
		if ( is_string( $mixed ) ) {
			return $this->from_string( $mixed );
		}
		if ( is_null( $mixed ) ) {
			return $this->from_string( '/' );
		}
	}

	public function from_array( $array ) {
		$return = [ ];
		foreach( $array as $item ) {
			$return[ ] = $this->from( $item );
		}
		return $return;
	}

	public function from_string( $string ) {
		$return = new $this;
		if ( \UString::is_start_with( $string, [ 'http://', 'https://', '//' ] ) ) {
			\UString::do_substr_after( $string, '//' );
			$return->host = \UString::substr_before( $string, [ '/', ':' ] );
			\UString::do_not_start_with( $string, $return->host );
			$return->port = 80;
		}
		if ( \UString::is_start_with( $string, ':' ) ) {
			\UString::do_not_start_with( $string, ':' );
			$return->port = intval( \UString::do_substr_after( $string, '/' ) );
		}
		\UString::do_not_end_with( $string, '/' );
		\UString::do_start_with( $string, '/' );
		$return->uri = $string;
		return $return;
	}

	public function by_server( ) {
		$return = new $this;
		if ( isset( $_SERVER[ 'SERVER_NAME' ] ) ) {
			$return->host = $_SERVER[ 'SERVER_NAME' ];
		} else {
			$return->host = 'localhost';
		}
		if ( isset( $_SERVER[ 'SERVER_PORT' ] ) ) {
			$return->port = intval( $_SERVER[ 'SERVER_PORT'] );
		} else {
			$return->port = '80';
		}
		if ( isset( $_SERVER[ 'REQUEST_URI' ] ) ) {
			$return->uri = \UString::substr_before( $_SERVER[ 'REQUEST_URI' ], '?' );
		} else {
			$return->uri = '/';
		}
		return $return;
	}


	/*************************************************************************
	  ACCESSOR METHODS                   
	 *************************************************************************/
	public function __toString( ) {
		return $this->to_string( );
	}

	public function to_string( ) {
		$url = '';
		if ( isset( $this->host ) ) {
			$url .= 'http://' . $this->host;
		}
		if ( isset( $this->port ) && $this->port != 80 ) {
			$url .= ':' . $this->port;
		}
		if ( isset( $this->uri ) && ! empty( $this->uri ) ) {
			$url .= $this->uri;
		}
		return $url;
	}

	public function match( $url ) {
		return ( 
			( is_null( $this->host ) || $this->host === $url->host ) &&
			( is_null( $this->port ) || $this->port === $url->port ) &&
			( is_null( $this->uri  ) || \UString::is_start_with( $url->uri, $this->uri )  )
		);
	}



	/*************************************************************************
	  TREATMENT METHODS                   
	 *************************************************************************/
	public function diff( $url ) {
		if ( is_object( $url ) ) {
			if ( isset( $url->host ) ) {
				unset( $url->host );
			}
			if ( isset( $url->port ) ) {
				unset( $url->port );
			}
			$this->diff_uri( $url );
		}
		return $this;
	}



	/*************************************************************************
	  URI TREATMENT METHODS                   
	 *************************************************************************/
	public function diff_uri( $url ) {
		if ( is_object( $url ) && ! empty( $url->uri ) ) {
			$this->uri = \Supersoniq\substr_after( $this->uri, $url->uri );
			\Supersoniq\must_starts_with( $this->uri, '/' );
		}
		return $this;
	}

	public function reset_uri( ) {
		$this->uri = '';
		return $this;
	}

	public function add_uri( $url ) {
		if ( is_object( $url ) && isset( $url->uri ) ) {
			$this->uri .= $url->uri;
		}
		return $this;
	}
}

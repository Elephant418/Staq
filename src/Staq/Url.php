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
			return $this->fromArray( $mixed );
		}
		if ( is_string( $mixed ) ) {
			return $this->fromString( $mixed );
		}
		if ( is_null( $mixed ) ) {
			return $this->fromString( '/' );
		}
	}

	public function fromArray( $array ) {
		$return = [ ];
		foreach( $array as $item ) {
			$return[ ] = $this->from( $item );
		}
		return $return;
	}

	public function fromString( $string ) {
		$return = new $this;
		if ( \UString::isStartWith( $string, [ 'http://', 'https://', '//' ] ) ) {
			\UString::doSubstrAfter( $string, '//' );
			$return->host = \UString::substrBefore( $string, [ '/', ':' ] );
			\UString::doNotStartWith( $string, $return->host );
			$return->port = 80;
		}
		if ( \UString::isStartWith( $string, ':' ) ) {
			\UString::doNotStartWith( $string, ':' );
			$return->port = intval( \UString::doSubstrAfter( $string, '/' ) );
		}
		\UString::doNotEndWith( $string, '/' );
		\UString::doStartWith( $string, '/' );
		$return->uri = $string;
		return $return;
	}

	public function byServer( ) {
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
			$return->uri = \UString::substrBefore( $_SERVER[ 'REQUEST_URI' ], '?' );
		} else {
			$return->uri = '/';
		}
		return $return;
	}


	/*************************************************************************
	  ACCESSOR METHODS                   
	 *************************************************************************/
	public function __toString( ) {
		return $this->toString( );
	}

	public function toString( ) {
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
			( is_null( $this->uri  ) || \UString::isStartWith( $url->uri, $this->uri )  )
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
			$this->diffUri( $url );
		}
		return $this;
	}



	/*************************************************************************
	  URI TREATMENT METHODS                   
	 *************************************************************************/
	public function diffUri( $url ) {
		if ( is_object( $url ) && ! empty( $url->uri ) ) {
			$this->uri = \Supersoniq\substr_after( $this->uri, $url->uri );
			\Supersoniq\must_starts_with( $this->uri, '/' );
		}
		return $this;
	}

	public function resetUri( ) {
		$this->uri = '';
		return $this;
	}

	public function addUri( $url ) {
		if ( is_object( $url ) && isset( $url->uri ) ) {
			$this->uri .= $url->uri;
		}
		return $this;
	}
}

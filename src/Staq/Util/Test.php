<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util;

class Test {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $name;
	public $result = FALSE;
	public $exception;



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $name, $test ) {
		$this->name = $name;
		try {
			$this->result = ( $test( ) === TRUE );
		} catch ( \Exception $e ) {
			$this->exception = $e;
		}
	}



	/*************************************************************************
	  OUPUT METHOD
	 *************************************************************************/
	public function output( ) {
		return ( $this->is_cli( ) ? $this->to_string( ) : $this->to_html( ) );
	}



	/*************************************************************************
	  PROTECTED METHOD
	 *************************************************************************/
	protected function is_cli( ) {
		return ( PHP_SAPI === 'cli' );
	}
	protected function to_html( ) {
		$str = $this->name;
		if ( ! $this->result ) {
			$str .= ': <b>ERROR</b>';
			if ( is_object( $this->exception ) ) {
				$str .= ' (' . $this->exception->get_message( ) . ')';
			}
		}
		return $str;
	}
	protected function to_string( ) {
		$string = $this->to_html( );
		$string = str_replace( [ '<b>', '</b>' ], '*'    , $string );
		$string = str_replace( [ '<br>', '<br/>', '</li>' ], PHP_EOL, $string );

		$match = [ ];
		while ( strpos( $string, '</ul>' ) ) {
			$target = substr( $string, 0, strpos( $string, '</ul>' ) + 5 );
			$target = substr( $target, strrpos( $target, '<ul>' ) );

			$replace = substr( $target, 4, strlen( $target ) - 9 );
			$replace = PHP_EOL . str_replace( '<li>', ' | <li>', $replace );
			$first   = strpos( $replace, '<li>' );

			$string = str_replace( $target, $replace, $string );
			preg_replace( '#<li>#', '<li>', $string );
		}
		$string = str_replace( '  <li> ', '  <li> |> ', $string );
		$string = preg_replace( '/[' . PHP_EOL . ']+/s', PHP_EOL, $string );
		return strip_tags( $string ) . PHP_EOL;
	}
}
<?php

/* Todo MIT license
 */

namespace Staq\util;
require_once( __DIR__ . '/functions.php' );

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
			$this->result = ( $test( ) == TRUE );
		} catch ( Exception $e ) {
			$this->exception = $e;
		}
	}



	/*************************************************************************
	  OUPUT METHOD
	 *************************************************************************/
	public function to_html( ) {
		return $this->name .': ' . ( $this->result ? 'OK' : 'ERROR' );
	}
}

class Test_Case extends Test {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $name;
	public $result = FALSE;
	public $tests = [ ];



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $name, $tests ) {
		$this->name = $name;
		foreach ( $tests as $name => $test ) {
			$this->tests[ ] = new \Staq\util\Test( $name, $test );
		}
		$this->compute( );
	}
	protected function compute( ) {
		$this->result = array_reduce( $this->tests, function( &$result, $item ){ return $result && $item->result; }, TRUE );
	}



	/*************************************************************************
	  OUPUT METHOD
	 *************************************************************************/
	public function to_html( ) {
		$html = parent::to_html( ) . '<ul>';
		foreach ( $this->tests as $test ) {
			$html .=  '<li>' . $test->to_html( ) . '</li>';
		}
		return $html . '</ul>';
	}
}

class Test_Collection extends Test_Case {



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $name, $test_cases ) {
		$this->name = $name;
		foreach ( $test_cases as $test_case ) {
			ob_start();		
			$result = ( include( $test_case ) );
			ob_end_clean();
			$this->tests[ ] = $result;
		}
		$this->compute( );
	}
}
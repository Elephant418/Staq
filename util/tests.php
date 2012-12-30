<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util;
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
	public $folder = '.';
	public $tests = [ ];



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $name, $tests ) {
		$this->name = $name;
		foreach ( $tests as $name => $test ) {
			$this->tests[ ] = new \Staq\Util\Test( $name, $test );
		}
		$this->compute( );
	}
	protected function compute( ) {
		$this->result = array_reduce( $this->tests, function( &$result, $item ){ return $result && $item->result; }, TRUE );
	}



	/*************************************************************************
	  OUPUT METHOD
	 *************************************************************************/
	public function to_html( $path = '' ) {
		$path .= $this->folder . '/';
		$html = '<a href="' . $path . '">' . parent::to_html( ) . '</a><ul>';
		foreach ( $this->tests as $test ) {
			$html .=  '<li>' . $test->to_html( $path ) . '</li>';
		}
		return $html . '</ul>';
	}
}

class Test_Collection extends Test_Case {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $tests = [ ];



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $name, $test_cases, $dir ) {
		$this->name = $name;
		foreach ( $test_cases as $test_case ) {
			ob_start();		
			$result = ( include( $dir . '/' . $test_case . '/index.php' ) );
			ob_end_clean();
			if ( is_a( $result, 'Staq\\Util\\Test_Case' ) ) {
				$result->folder = $test_case;
			}
			$this->tests[ ] = $result;
		}
		$this->compute( );
	}
}
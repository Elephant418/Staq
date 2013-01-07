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
		} catch ( \Exception $e ) {
			$this->exception = $e;
		}
	}



	/*************************************************************************
	  OUPUT METHOD
	 *************************************************************************/
	public function to_html( ) {
		$str = $this->name;
		if ( ! $this->result ) {
			$str .= ': <b>ERROR</b>';
			if ( is_object( $this->exception ) ) {
				$str .= ' (' . $this->exception->get_message( ) . ')';
			}
		}
		return $str;
	}
}

class Test_Case extends Test {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $folder = '.';
	public $ok = 0;
	public $error = 0;



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $name, $tests ) {
		$this->name = $name;
		foreach ( $tests as $name => $test ) {
			$test = new \Staq\Util\Test( $name, $test );
			if ( $test->result ) {
				$this->ok++;
			} else {
				$this->error++;
			}
			$this->tests[ ] = $test;
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
		$html = '<a href="' . $path . '">' . $this->name . '</a> ' . $this->ok;
		if ( $path == '' || $this->error > 0 || isset( $_GET[ 'all' ] ) ) {
			$html .= ' / ' . $this->error . '<ul>';
			foreach ( $this->tests as $test ) {
				$html .=  '<li>' . $test->to_html( $path ) . '</li>';
			}
			$html .= '</ul>';
		}
		if ( $path == './' ) {
			$path  = \Staq\Util\string_substr_before( $_SERVER[ 'REQUEST_URI' ], '?' );
			$get   = $_GET;
			if ( isset( $_GET[ 'all' ] ) ) {
				unset( $get['all'] );
				$label = 'Hide successful tests';
			} else {
				$get['all'] = TRUE;
				$label = 'Show all tests';
			}
			$url   = $path . '?' . http_build_query( $get );
			$html .= '<br /><br /><a href="' . $url . '">' . $label . '</a>';
		}
		return $html;
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
			$this->ok += $result->ok;
			$this->error += $result->error;
			$this->tests[ ] = $result;
		}
		$this->compute( );
	}
}
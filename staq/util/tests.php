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

class Test_Case extends Test {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $tests  = [ ];
	public $folder = '.';
	public $ok     = 0;
	public $error  = 0;



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
	protected function to_html( $path = '' ) {
		$path .= $this->folder . '/';
		$html = '<a href="' . $path . '">' . $this->name . '</a>   ' . $this->ok . '+';
		if ( $path == '' || $this->error > 0 || $this->is_all_asked( ) ) {
			$html .= ' ' . $this->error . '-<ul>';
			foreach ( $this->tests as $test ) {
				$html .=  '<li> ' . $test->to_html( $path ) . '</li>';
			}
			$html .= '</ul>';
		}
		if ( $path == './' && ! $this->is_cli( ) ) {
			$path  = \UString\substr_before( $_SERVER[ 'REQUEST_URI' ], '?' );
			$get   = $_GET;
			if ( $this->is_all_asked( ) ) {
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

	protected function is_all_asked( ) {
		if ( $this->is_cli( ) ) {
			global $argv;
			return in_array( 'all', $argv );
		}
		return isset( $_GET[ 'all' ] );
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
		foreach ( $test_cases as $case_folder ) {
			ob_start( );
			$case = ( include( $dir . '/' . $case_folder . '/index.php' ) );
			ob_end_clean( );
			if ( is_a( $case, 'Staq\\Util\\Test_Case' ) ) {
				$case->folder = $case_folder;
			}
			$this->ok += $case->ok;
			$this->error += $case->error;
			$this->tests[ ] = $case;
		}
		$this->compute( );
	}
}
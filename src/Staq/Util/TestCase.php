<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util;

class TestCase extends Test {



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
			$test = new \Staq\Util::Test( $name, $test );
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
		if ( $path === '' || $this->error > 0 || $this->is_all_asked( ) ) {
			$html .= ' ' . $this->error . '-<ul>';
			foreach ( $this->tests as $test ) {
				$html .=  '<li> ' . $test->to_html( $path ) . '</li>';
			}
			$html .= '</ul>';
		}
		if ( $path === './' && ! $this->is_cli( ) ) {
			$path  = \UString::substr_before( $_SERVER[ 'REQUEST_URI' ], '?' );
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
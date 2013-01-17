<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util;

class TestCollection extends Test_Case {



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
			if ( is_a( $case, 'Staq\\Util\\TestCase' ) ) {
				$case->folder = $case_folder;
			}
			$this->ok += $case->ok;
			$this->error += $case->error;
			$this->tests[ ] = $case;
		}
		$this->compute( );
	}
}
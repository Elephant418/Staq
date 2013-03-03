<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class StaqTestCase extends \PHPUnit_Framework_TestCase {



	/*************************************************************************
	  ATTRIBUTES
	 *************************************************************************/
	public $project_namespace = 'Test\\Staq\\Project\\';



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( ) {

		// Initialize project namespace
		$project_name = \UObject::getClassName( $this );
		\UString::doNotEndWith( $project_name, 'Test' );
		$this->project_namespace .= $project_name;
	}



	/*************************************************************************
	  UTIL METHODS             
	 *************************************************************************/
	public function getProjectClass( $name ) {
		return $this->project_namespace . '\\' . $name;
	}

	public function getProjectStackClass( $name ) {
		return $this->getProjectClass( 'Stack\\' . $name );
	}
}
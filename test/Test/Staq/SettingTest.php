<?php

namespace Test\Staq;

require_once( __DIR__ . '/../../../vendor/autoload.php' );

class SettingTest extends \PHPUnit_Framework_TestCase {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $project_namespace = 'Test\\Staq\\Project\\Setting';



	/*************************************************************************
	  UTIL METHODS             
	 *************************************************************************/
	public function get_project_class( $name ) {
		return $this->project_namespace . '\\' . $name;
	}

	public function get_project_stack_class( $name ) {
		return $this->get_project_class( 'Stack\\' . $name );
	}



	/*************************************************************************
	  TEST METHODS             
	 *************************************************************************/
	public function test_unexisting_setting( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		$setting = ( new \Stack\Setting )->parse( 'test' );
		$this->assertEquals( 'a_value', $setting[ 'test.a_setting' ] );
	}

	public function test_existing_setting__existing_key( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		$setting = ( new \Stack\Setting )->parse( 'application' );
		$this->assertEquals( 0, $setting[ 'error.display_errors' ] );
	}

	public function test_existing_setting__custom_key( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		$setting = ( new \Stack\Setting )->parse( 'application' );
		$this->assertEquals( 'a_value', $setting[ 'error.a_setting' ] );
	}

	public function test_existing_setting__inherit_key__extension( ) {
		$app = \Staq\Application::create( $this->project_namespace );
		$setting = ( new \Stack\Setting )->parse( 'application' );
		$this->assertEquals( 'E_STRICT', $setting[ 'error.error_reporting' ] );
	}

	public function test_existing_setting__inherit_key__platform( ) {
		$app = \Staq\Application::create( $this->project_namespace, '/', 'local' );
		$setting = ( new \Stack\Setting )->parse( 'application' );
		$this->assertEquals( 1, $setting[ 'error.display_errors' ] );
	}

	public function test_existing_setting__merged_key__platform( ) {
		$app = \Staq\Application::create( $this->project_namespace, '/', 'local' );
		$setting = ( new \Stack\Setting )->parse( 'test' );
		$this->assertEquals( [ 'a_value', 'more_value' ], $setting[ 'test.a_setting' ] );
	}
}
<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/Staq';
require_once( $staq_path . '/util/tests.php' );
require_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 6 );
$app = \Staq\Application::create( $path );
$change_platform = function( $platform ) {
	\Staq\Application::current_application( )
		->set_platform( $platform );
};

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Setting', [
	'Fetch a value from an existing setting file' => function( ) {
		$setting = new \Stack\Setting( 'application' );
		\Staq\Util\stack_debug( $setting );
		return ( $setting->get_as_boolean( 'error', 'display_errors' ) == FALSE );
	},
	'Fetch a value from a custom setting file' => function( ) {
		$setting = new \Stack\Setting( 'test' );
		return ( $setting->get_as_boolean( 'test', 'a_setting' ) == 'a_value' );
	},
	'Fetch a value merged with an inherited extension' => function( ) {
		$setting = new \Stack\Setting( 'application' );
		return ( $setting->get_as_boolean( 'error', 'a_setting' ) == 'a_value' );
	},
	'Fetch a value with an existing platform' => function( ) use ( $change_platform ) {
		$change_platform( 'local' );
		$setting = new \Stack\Setting( 'application' );
		return ( $setting->get_as_boolean( 'error', 'display_errors' ) == TRUE );
	},
	'Fetch a value merged with a custom platform' => function( ) use ( $change_platform ) {
		$change_platform( 'titan' );
		$setting = new \Stack\Setting( 'application' );
		return ( $setting->get_as_boolean( 'error', 'error_reporting' ) == 'CHIPS' );
	},
	'Fetch a value merged with an inherited platform' => function( ) use ( $change_platform ) {
		$change_platform( 'local.coco' );
		$setting = new \Stack\Setting( 'application' );
		return ( $setting->get_as_boolean( 'error', 'error_reporting' ) == 'E_ALL' );
	}
] );

// RESULT
echo $case->output( );
return $case;
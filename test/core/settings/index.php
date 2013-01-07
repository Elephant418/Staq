<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/Staq';
require_once( $staq_path . '/util/tests.php' );
require_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 6 );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Parsing inherit setting files', [
	'Error values for a production site' => function( ) use ( $path ) {
		$app = \Staq\Application::create( $path );
		$settings = new \Stack\Settings( 'application' );
		return ( $settings->get_boolean( 'error', 'display_errors' ) == FALSE );
	},
	'Error values for a local site' => function( ) use ( $path ) {
		$app = \Staq\Application::create( $path, '/', 'local' );
		$settings = new \Stack\Settings( 'application' );
		return ( $settings->get_boolean( 'error', 'display_errors' ) == TRUE );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
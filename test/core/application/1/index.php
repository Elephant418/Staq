<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/staq';
require_once( $staq_path . '/util/tests.php' );
require_once( $staq_path . '/include.php' );

// CONTEXT
$app = \Staq\Application::create( );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Without custom application', [
	'Extensions' => function( ) use ( $app ) {
		return ( $app->get_extensions( ) == [ 'staq/app/starter', 'staq/core/view', 'staq/core/router', 'staq/core/ground' ] );
	},
	'Platform'   => function( ) use ( $app ) {
		return ( $app->get_platform( ) == 'prod' );
	}
] );

// RESULT
echo $case->output( );
return $case;
<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 );
require_once( $staq_path . '/util/tests.php' );
include_once( $staq_path . '/include.php' );

// CONTEXT
$app = new \Staq\Application( );
$app->run( );

// TEST COLLECTION
$case = new \Staq\util\Test_Case( 'Without custom application', [
	'Extensions' => function( ) use ( $app ) {
		return ( $app->get_extensions( ) == [ 'Staq/starter', 'Staq/view', 'Staq/ground' ] );
	},
	'Platform'   => function( ) use ( $app ) {
		return ( $app->get_platform( ) == 'prod' );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
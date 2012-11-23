<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 );
require_once( $staq_path . '/util/tests.php' );
include_once( $staq_path . '/include.php' );

// CONTEXT
$path = \Staq\util\string_basename( __DIR__, 4 );
$app = new \Staq\Application( $path );
$app->run( );

// TEST COLLECTION
$case = new \Staq\util\Test_Case( 'With default configuration', [
	'Extensions' => function( ) use ( $app, $path ) {
		return ( $app->get_extensions( ) == [ $path, 'Staq/starter', 'Staq/view', 'Staq/ground' ] );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
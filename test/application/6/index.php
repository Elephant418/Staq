<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 );
require_once( $staq_path . '/util/tests.php' );
include_once( $staq_path . '/include.php' );

// CONTEXT
$path = \Staq\util\string_basename( __DIR__, 4 );
$app = new \Staq\Application( $path );
$app->start( );
new \Stack\Coco;

// TEST COLLECTION
$case = new \Staq\util\Test_Case( 'With the starter disabled', [
	'Extensions' => function( ) use ( $app, $path ) {
		return ( $app->get_extensions( ) == [ $path, 'Staq/view', 'Staq/ground' ] );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
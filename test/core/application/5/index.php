<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/staq';
require_once( $staq_path . '/util/tests.php' );
require_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 6 );
$app = \Staq\Application::create( $path );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'With the starter disabled', [
	'Extensions' => function( ) use ( $app, $path ) {
		return ( $app->get_extensions( ) == [ $path, 'staq/core/ground' ] );
	}
] );

// RESULT
echo $case->output( );
return $case;
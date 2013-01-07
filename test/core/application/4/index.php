<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/Staq';
require_once( $staq_path . '/util/tests.php' );
require_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 6 );
$app = \Staq\Application::create( $path );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Extends project without configuration', [
	'Extensions' => function( ) use ( $app, $path ) {
		return ( $app->get_extensions( ) == [ $path, 'Staq/unexisting', 'Staq/app/starter', 'Staq/core/view', 'Staq/core/router', 'Staq/core/ground' ] );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
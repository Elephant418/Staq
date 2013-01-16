<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/vendor/pixel418/staq/src';
require_once( $staq_path . '/util/tests.php' );
require_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 6 );
$app = \Staq\Application::create( $path );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Extends project without configuration', [
	'Extensions' => function( ) use ( $app, $path ) {
		return ( $app->get_extensions( 'name' ) == [ $path, 'Staq/unexisting', 'staq/app/starter', 'staq/core/view', 'staq/core/router', 'staq/core/ground' ] );
	}
] );

// RESULT
echo $case->output( );
return $case;
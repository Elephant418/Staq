<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/vendor/pixel418/staq/src';
require_once( $staq_path . '/util/tests.php' );
require_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 6 );
$app = \Staq\Application::create( $path );

// TEST COLLECTION
$case = new \Staq\Util\TestCase( 'With default configuration', [
	'Extensions' => function( ) use ( $app, $path ) {
		return ( $app->get_extensions( 'name' ) == [ $path, 'Staq\App\Starter', 'Staq\Core\Router', 'Staq\Core\Ground' ] );
	}
] );

// RESULT
echo $case->output( );
return $case;
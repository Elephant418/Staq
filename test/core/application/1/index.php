<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 );
require_once( $staq_path . '/util/tests.php' );
require_once( $staq_path . '/include.php' );

// CONTEXT
$app = \Staq\application( );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Without custom application', [
	'Extensions' => function( ) use ( $app ) {
		return ( $app->get_extensions( ) == [ 'Staq/app/starter', 'Staq/core/view', 'Staq/core/router', 'Staq/core/ground' ] );
	},
	'Platform'   => function( ) use ( $app ) {
		return ( $app->get_platform( ) == 'prod' );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
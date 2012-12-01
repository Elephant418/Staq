<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 );
require_once( $staq_path . '/util/tests.php' );
include_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 1 );
$app = new \Staq\Application( $path );
$app->start( );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Autoload of unexisting class', [
	'Single part' => function( ) {
		$stack = NULL;
		try {
			$stack = new \Stack\Coco;
		} catch( Exception $e ) { }
		return ( get_class( $stack ) == 'Stack\\Coco' );
	},
	'Multiple parts' => function( ) {
		$stack = NULL;
		try {
			$stack = new \Stack\Coco\Des\Bois;
		} catch( Exception $e ) { }
		return ( get_class( $stack ) == 'Stack\\Coco\\Des\\Bois' );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
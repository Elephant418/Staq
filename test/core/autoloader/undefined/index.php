<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 );
require_once( $staq_path . '/util/tests.php' );
include_once( $staq_path . '/include.php' );

// CONTEXT
$path = \Staq\util\string_basename( __DIR__, 4 );
$app = new \Staq\Application( $path );
$app->start( );

// TEST COLLECTION
$case = new \Staq\util\Test_Case( 'Autoload of unexisting class', [
	'Unexisting' => function( ) {
		$stack = NULL;
		try {
			$stack = new \Stack\Coco;
		} catch( Exception $e ) { }
		return ( get_class( $stack ) == 'Stack\\Coco' );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
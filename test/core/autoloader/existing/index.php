<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 );
require_once( $staq_path . '/util/tests.php' );
include_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 1 );
$app = new \Staq\Application( $path );
$app->start( );
$stack = NULL;
try {
	$stack = new \Stack\Model\Coco;
} catch( Exception $e ) { }
print_r( $stack );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Autoload of existing class', [
	'Object' => function( ) {
		return ( get_class( $stack ) == 'Stack\\Model\\Coco' );
	},
	'Attribute' => function( ) {
		return ( $stack->attribute == 'ok' );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
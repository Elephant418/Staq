<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 );
require_once( $staq_path . '/util/tests.php' );
include_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 1 );
$app = new \Staq\Application( $path );
$app->start( );
$stack = NULL;
$stack2 = NULL;
try {
	$stack = new \Stack\Model\Coco;
	$stack2 = new \Stack\Model\Coco\Des\Bois;
} catch( Exception $e ) { }

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Autoload of existing class', [
	'Object' => function( ) use ( $stack ) {
		return ( get_class( $stack ) == 'Stack\\Model\\Coco' );
	},
	'Attribute' => function( ) use ( $stack ) {
		return ( $stack->attribute == 'ok' );
	},
	'Complex' => function( ) use ( $stack2 ) {
		return ( is_a( $stack2, 'Staq\Test\Core\Autoloader\Existing\Stack\Model\Coco' ) );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 );
require_once( $staq_path . '/util/tests.php' );
require_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 1 );
$app = \Staq\application( $path );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Stack autoloading without existing class', [
	'You can instanciate an empty simple stack' => function( ) {
		$stack = new \Stack\Coco;
		return ( get_class( $stack ) == 'Stack\\Coco' && \Staq\Util\stack_height( $stack ) == 0 );
	},
	'You can instanciate an empty complex stack' => function( ) {
		$stack = new \Stack\Coco\Des\Bois;
		return ( get_class( $stack ) == 'Stack\\Coco\\Des\\Bois' && \Staq\Util\stack_height( $stack ) == 0 );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
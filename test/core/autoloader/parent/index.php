<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 );
require_once( $staq_path . '/util/tests.php' );
include_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 1 );
$app = new \Staq\Application( $path );
$app->start( );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Stack queries', [
	'An unknown stack query give an empty stack' => function( ) {
		$stack = new \Stack\Machin\Coco;
		return ( \Staq\Util\get_stack_definition_classes( $stack ) == [ ] );
	},
	'An unknown controller stack query give a stack with the default controller' => function( ) {
		$stack = new \Stack\Controller\Coco;
		return ( \Staq\Util\get_stack_definition_classes( $stack ) == [ 'Staq\Ground\Stack\Controller\__Default' ] );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
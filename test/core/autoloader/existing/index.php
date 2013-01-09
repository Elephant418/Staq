<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/Staq';
require_once( $staq_path . '/util/tests.php' );
require_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 6 );
$app = \Staq\Application::create( $path );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Stack autoloading with an existing class', [
	'A defined stackable class is stacked when we have a perfect matching query' => function( ) {
		$stack = new \Stack\Model\Coco;
		return ( \Staq\Util\stack_definition_contains( $stack, 'Test\Core\Autoloader\Existing\Stack\Model\Coco' ) );
	},
	'A defined stackable class is stacked when we have a more specific query' => function( ) {
		$stack = new \Stack\Model\Coco\Des\Bois;
		// \Staq\Util\stack_debug( $stack );
		return ( \Staq\Util\stack_definition_contains( $stack, 'Test\Core\Autoloader\Existing\Stack\Model\Coco' ) );
	},
	'You can access to an attribute of a stacked class' => function( ) {
		$stack = new \Stack\Model\Coco;
		return ( $stack->attribute == 'ok' );
	}
] );

// RESULT
echo $case->output( );
return $case;
<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/staq';
require_once( $staq_path . '/util/tests.php' );
require_once( $staq_path . '/include.php' );

// CONTEXT
$path = substr( __DIR__, strrpos( __DIR__, '/Staq/' ) + 6 );
$app = \Staq\Application::create( $path );

// TEST COLLECTION
$case = new \Staq\Util\Test_Case( 'Stack autoloading with an existing parent', [
	'Query an unknown stack give an empty stack' => function( ) {
		$stack = new \Stack\Machin\Coco;
		return ( \Staq\Util\stack_height( $stack ) == 0 );
	},
	'Query an unknown controller stack give a stack with the default controller' => function( ) {
		$stack = new \Stack\Controller\Coco;
		return ( \Staq\Util\stack_definition_contains( $stack, 'Staq\Core\Router\Stack\Controller' ) );
	},
	'Query a defined controller stack give a stack with the defined & default controller' => function( ) {
		$stack = new \Stack\Controller\About;
		// \Staq\Util\stack_debug( $stack );
		return ( 
			\Staq\Util\stack_definition_contains( $stack, 'Test\Core\Autoloader\Parent\Stack\Controller\About' ) &&
			\Staq\Util\stack_definition_contains( $stack, 'Staq\Core\Router\Stack\Controller' ) 
		);
	},
	'Query a defined stack element without define parent give a stack with a height of 1' => function( ) {
		$stack = new \Stack\Machin\About;
		return ( \Staq\Util\stack_height( $stack ) == 1 );
	},
	'Query a redefined default exception give a stack with with the two default exception' => function( ) {
		$stack = new \Stack\Exception\Unexisting_exception;
		return ( \Staq\Util\stack_height( $stack ) == 2 );
	}
] );

// RESULT
echo $case->output( );
return $case;
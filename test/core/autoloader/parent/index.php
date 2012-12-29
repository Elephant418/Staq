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
	'No parent' => function( ) {
		$stack = NULL;
		try {
			$stack = new \Stack\Machin\Coco;
		} catch( Exception $e ) { }
		return ( \Staq\Util\get_stack_definition_classes( $stack ) == [ ] );
	},
	'With parent' => function( ) {
		$stack = NULL;
		try {
			$stack = new \Stack\Controller\Coco;
		} catch( Exception $e ) { }
		return ( \Staq\Util\get_stack_definition_classes( $stack ) == [ 'Staq\Ground\Controller\__Default' ] );
	}
] );

// RESULT
echo $case->to_html( );
return $case;
<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 );
require_once( $staq_path . '/util/tests.php' );

// DEFINITION
$name  = 'Application';
$test_cases = [ '1', '2', '3', '4', '5' ];

// COLLECTION
$files = array_map( function( $item ) { return __DIR__ . '/' . $item . '/index.php'; }, $test_cases );
$collection = new \Staq\util\Test_Collection( $name, $files );

// RESULT
echo $collection->to_html( );
return $collection;
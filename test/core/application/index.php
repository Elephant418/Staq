<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/vendor/pixel418/staq/src';
require_once( $staq_path . '/util/tests.php' );

// DEFINITION
$name  = 'Application';
$test_cases = [ 1, 2, 3, 4, 5 ];

// COLLECTION
$collection = new \Staq\Util\Test_Collection( $name, $test_cases, __DIR__ );

// RESULT
echo $collection->output( );
return $collection;
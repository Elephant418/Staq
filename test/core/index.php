<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/Staq';
require_once( $staq_path . '/util/tests.php' );

// DEFINITION
$name  = 'Core';
$test_cases = [ 'application', 'autoloader' ];

// COLLECTION
$collection = new \Staq\Util\Test_Collection( $name, $test_cases, __DIR__ );

// RESULT
echo $collection->to_html( );
return $collection;
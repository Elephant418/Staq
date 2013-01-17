<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/vendor/pixel418/staq/src';
require_once( $staq_path . '/util/tests.php' );

// DEFINITION
$name  = 'Core';
$test_cases = [ 'application', 'autoloader', 'setting' ];

// COLLECTION
$collection = new \Staq\Util\TestCollection( $name, $test_cases, __DIR__ );

// RESULT
echo $collection->output( );
return $collection;
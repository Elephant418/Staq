<?php

$staq_path = substr( __DIR__, 0, strrpos( __DIR__, '/Staq/' ) + 5 ) . '/vendor/pixel418/staq/staq';
require_once( $staq_path . '/util/tests.php' );

// DEFINITION
$name  = 'Staq';
$test_cases = [ 'core' ];

// COLLECTION
$collection = new \Staq\Util\Test_Collection( $name, $test_cases, __DIR__ );

// RESULT
echo $collection->output( );
exit( $collection->error );
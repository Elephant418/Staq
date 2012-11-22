<?php

$tests = [ 'application/2' ];

foreach ( $tests as $test ) {
	ob_start();		
	$result = ( include( __DIR__ . '/' . $test . '/index.php' ) );
	ob_end_clean();
	var_dump( $result );
}

/*
echo '<h1>' . $title . '</h1><p>Tests : </p><ul>';
foreach ( $tests as $name => $result ) {
	echo '<li>' . $name .': ' . ( $result ? 'OK' : 'ERROR' ) . '</li>';
}
echo '</ul>';

return array_reduce( $tests, function( &$result, $item ){ return $result && $item; }, TRUE );
*/
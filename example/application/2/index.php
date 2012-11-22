<?php

$title = 'Empty application without starter, display 404';

include_once( __DIR__ . '/../../../include.php' );

$title = ucfirst( \Staq\util\string_basename( __DIR__, 2 ) );
$path = \Staq\util\string_basename( __DIR__, 4 );
$tests = [ ];

$app = new \Staq\Application( $path );
$app->run( );

$tests[ 'Extensions' ] = ( $app->get_extensions( ) == [ 'Staq/example/application/2', 'Staq/starter', 'Staq/view', 'Staq/ground' ] );
$tests[ 'Platform' ] = ( $app->get_platform( ) == 'prod' );

echo '<h1>' . $title . '</h1><p>Tests : </p><ul>';
foreach ( $tests as $name => $result ) {
	echo '<li>' . $name .': ' . ( $result ? 'OK' : 'ERROR' ) . '</li>';
}
echo '</ul>';

return array_reduce( $tests, function( &$result, $item ){ return $result && $item; }, TRUE );
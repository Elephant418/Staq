<?php

$title = 'Empty application, display starter';

include( '../../../include.php' );

echo '<h1>' . $title . '</h1>';
$app = new \Staq\Application;
$app->run( );

// \Staq\util\string_basename( __DIR__, 4 ) 
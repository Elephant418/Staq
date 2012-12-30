<?php

/* This file is part of the Staq project, which is under MIT license */


if ( ! defined( 'HTML_EOL' ) ) {
	define( 'HTML_EOL', '<br>' . PHP_EOL );
}
if ( ! defined( 'STAQ_ROOT_PATH' ) ) {
	define( 'STAQ_ROOT_PATH', dirname( __DIR__ ) . '/' );
}

include_once( __DIR__ . '/util/functions.php' );
include_once( __DIR__ . '/core/Application.php' );
include_once( __DIR__ . '/core/Autoloader.php' );
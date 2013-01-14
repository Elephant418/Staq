<?php

/* This file is part of the Staq project, which is under MIT license */

namespace Staq {
	const VERSION = '0.4';

	if ( ! defined( 'HTML_EOL' ) ) {
		define( 'HTML_EOL', '<br>' . PHP_EOL );
	}
	if ( ! defined( 'Staq\\VENDOR_ROOT_PATH' ) ) {
		if ( $pos = strrpos( __DIR__, '/vendor/' ) ) {
			define( 'Staq\\VENDOR_ROOT_PATH', substr( __DIR__, 0, $pos ) . '/vendor/' );
		} else {
			define( 'Staq\\VENDOR_ROOT_PATH', dirname( __DIR__ ) . '/vendor/' );
		}
	}
	if ( ! defined( 'Staq\\STAQ_ROOT_PATH' ) ) {
		define( 'Staq\\STAQ_ROOT_PATH', \Staq\VENDOR_ROOT_PATH . 'pixel418/staq/' );
	}
	if ( ! defined( 'Staq\\ROOT_PATH' ) ) {
		define( 'Staq\\ROOT_PATH', dirname( \Staq\VENDOR_ROOT_PATH ) . '/' );
	}

	require_once( __DIR__ . '/util/functions.php' );
	require_once( __DIR__ . '/../vendor/pixel418/ubiq/ubiq/Ubiq.php' );
	require_once( __DIR__ . '/core/Server.php' );
	require_once( __DIR__ . '/core/Application.php' );
	require_once( __DIR__ . '/core/Autoloader.php' );
}


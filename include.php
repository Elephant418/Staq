<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

session_start( );

define( 'HTML_EOL', '<br>' . PHP_EOL ) ;

$root_path = dirname( __FILE__) . '/Kernel/';
require_once( $root_path . 'utils.php' );
require_once( $root_path . 'Configuration.php' );
require_once( $root_path . 'Autoloader.php' );
require_once( $root_path . 'Application.php' );
require_once( $root_path . 'Supersoniq.php' );

$autoloader = new \Supersoniq\Autoloader( );
$autoloader->init( );

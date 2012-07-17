<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

include_once( '../../Supersoniq/include.php' );

require_once( dirname( __FILE__ ) . '/Supersoniq.php' );
( new \Supersoniq\Kernel\Test\Supersoniq( ) )->run( );






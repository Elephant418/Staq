<?php

require_once( __DIR__ . '/../../../../../vendor/autoload.php' );

\Staq\App::create( 'Example\\Staq\\Vanilla' )
	->set_platform( 'local' )
	->add_controller( '/', function( ) {
		return new \Stack\View\Home;
	} )
	->run( );

?>
<?php

require_once( __DIR__ . '/../../../../../vendor/autoload.php' );

\Staq\App::create( 'Example\\Staq\\Vanilla' )
	->setPlatform( 'local' )
	->addController( '/', function( ) {
		return new \Stack\View\Home;
	} )
	->run( );

?>
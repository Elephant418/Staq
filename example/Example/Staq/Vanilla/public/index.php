<?php

require_once( __DIR__ . '/../../../../../vendor/autoload.php' );

\Staq\App::create( 'Example\\Staq\\Vanilla' )
	->set_platform( 'local' )
	->add_controller( '/', function( ) {
		$page = new \Stack\View;
		$page[ 'template' ] = 'home.html';
		return $page;
	} )
	->run( );

?>
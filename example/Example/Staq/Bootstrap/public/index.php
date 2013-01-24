<?php

require_once( __DIR__ . '/../../../../../vendor/autoload.php' );

\Staq\Application::create( 'Example\\Staq\\Bootstrap' )
	->set_platform( 'local' )
	->add_controller( '/', function( ) {
		$page = new \Stack\View;
		return $page;
	} )
	->run( );

?>
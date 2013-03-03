<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack ;

class Router extends Router\__Parent {



	/*************************************************************************
	  PRIVATE METHODS             
	 *************************************************************************/
	protected function callController( $controller, $action, $route ) {
		$controllers = $this->setting->getAsArray( 'auth.controller' );
		$exclude = ( $this->setting[ 'auth.mode' ] == 'exclude' );
		$inner   = in_array( $controller, $controllers );
		if ( $exclude xor $inner ) {
			if ( ! \Staq::App()->getController( 'Auth' )->is_logged( ) ) {
				throw new \Stack\Exception\NotAllowed( );
			}
		}
		return parent::callController( $controller, $action, $route );
	}
}
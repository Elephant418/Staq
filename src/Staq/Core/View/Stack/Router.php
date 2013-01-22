<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\View\Stack ;

class Router extends Router\__Parent{


	/*************************************************************************
	  PRIVATE METHODS             
	 *************************************************************************/
	protected function render( $view ) {
		if ( is_a( $view, 'Stack\\View' ) ) {
			$view = $this->render( $view->render( ) );
		}
		return $view;
	}
}
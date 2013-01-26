<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack ;

class Router extends Router\__Parent{


	/*************************************************************************
	  PRIVATE METHODS             
	 *************************************************************************/
	protected function render( $model ) {
		if ( is_a( $model, 'Stack\\Model' ) ) {
			$page = new \Stack\View;
			$page[ 'content'  ] = $model;
			$page[ 'template' ] = 'model/' . \Staq\Util::stack_sub_query( $model, '/' );
		}
		return parent::render( $page );
	}
}
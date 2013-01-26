<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack ;

class Router extends Router\__Parent{


	/*************************************************************************
	  PRIVATE METHODS             
	 *************************************************************************/
	protected function render( $model ) {
		if ( \Staq\Util::is_stack( $model, 'Stack\\Model' ) ) {
			$page = new \Stack\View;
			$page[ 'content'  ] = $model;
			$page[ 'template' ] = 'model/' . strtolower( \Staq\Util::stack_sub_query( $model, '/' ) ) . '.html';
		} else {
			$page = $model;
		}
		return parent::render( $page );
	}
}
<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\View\Module\Model;

abstract class All extends All\__Parent {


	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
	public function fill( $template, $parameters = [ ] ) {
		$models = [ ];
		if ( 
			isset( $_GET[ 'from' ][ 'type' ] ) &&
			isset( $_GET[ 'from' ][ 'id'   ] ) &&
			isset( $_GET[ 'from' ][ 'attribute' ] ) 
		) {
			$template->from = ( new \Model )
				->by_type( $_GET[ 'from' ][ 'type' ] )
				->by_id( $_GET[ 'from' ][ 'id'   ] );
			$models = $template->from->get( $_GET[ 'from' ][ 'attribute' ] );
		} else {
			$models = $this->get_controller( )->all( );
		}
		$template->models = $models;
		$template->model_subtypes = $this->get_controller( )->get_subtype( );
		return $template;
	}

}

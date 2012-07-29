<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\View\Module\Model;

abstract class All extends All\__Parent {


	/*************************************************************************
	  ACTION METHODS                   
	 *************************************************************************/
        public function fill( $template ) {
		$template->models = $this->get_controller( )->all( );
		return $template;
	}



	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function get_controller( ) {
		return ( new \Controller )->by_name( 'Model\\' . $this->get_model_name( ) );
	}
	protected function get_model_name( ) {
		return \Supersoniq\substr_after( \Supersoniq\substr_after( \Supersoniq\substr_after( $this->type, '\\' ), '\\' ), '\\' );
	}

}

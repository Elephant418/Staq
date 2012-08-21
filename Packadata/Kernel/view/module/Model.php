<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\View\Module;

abstract class Model extends Model\__Parent {



	/*************************************************************************
	  PUBLIC METHODS                   
	 *************************************************************************/
	public function render( $parameters = [ ] ) {
		$template = $this->get_template( );
		$template->model_type = $this->get_model_name( );
		return $this->fill( $template, $parameters );
	}



	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function get_controller( $subtype = '' ) {
		$name = $this->get_model_name( );
		if ( $subtype ) {
			$name .= '\\' . ucfirst( $subtype );
		}
		return ( new \Controller )->by_name( 'Model\\' . $name );
	}
	protected function get_model_name( ) {
		return \Supersoniq\substr_after( \Supersoniq\substr_after( \Supersoniq\substr_after( $this->type, '\\' ), '\\' ), '\\' );
	}

}

<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Qrumble\View;

class __Base {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $type;



	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {
		$this->type = \Supersoniq\class_type_name( $this );
	}



	/*************************************************************************
	  PUBLIC METHODS                   
	 *************************************************************************/
	public function render( $parameters = [ ] ) {
		$template = $this->get_template( );
		return $this->fill( $template, $parameters );
	}



	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function fill( $template, $parameters = [ ] ) {
		return $template;
	}

	protected function get_template( $must_exists = TRUE ) {
		$template = ( new \Template )->by_name( $this->type );
		if ( $must_exists && ! $template->is_template_found( ) ) {
			throw new \Exception\Resource_Not_Found( 'Template not found "' . $this->type . '"' );
		}
		return $template;
	}
}

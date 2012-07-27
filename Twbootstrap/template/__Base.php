<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Twbootstrap\Template;

class __Base {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $parent;
	public $extensions = [ 'html' ];
	public $type;
	public $path;



	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {
		$this->type = \Supersoniq\class_type_name( $this );
		$this->path = $this->get_template_path( );
		if ( \Supersoniq\starts_with( $this->type, 'Module\\' ) ) {
			$this->parent = ( new \View )->by_layout( 'simple' )->get_template( );
			$this->parent->content = $this;
		}
	}
	public function compile( ) {
		if ( is_object( $this->parent ) ) {
			return $this->parent->compile( );
		}
		return $this;
	}



	/*************************************************************************
	  PUBLIC METHODS                   
	 *************************************************************************/
	public function render( ) {
		return file_get_contents( $this->path );
	}

	public function is_template_found( ) {
		return is_file( $this->path );
	}



	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function get_template_path( ) {
		$name = \Supersoniq\format_to_path( strtolower( $this->type ) );
		foreach ( \Supersoniq::$DESIGNS as $design ) {
			foreach ( $this->extensions as $extension ) {
				$template_path = $this->get_template_path_by_design_and_extension( $name, $design, $extension );
				if ( is_file( $template_path ) ) {
					return $template_path;
				}
			}
		}
		return FALSE;
	}

	protected function get_template_path_by_design_and_extension( $name, $design, $extension ) {
		return SUPERSONIQ_ROOT_PATH . $design . '/template/' . $name . '.' . $extension;
	}
}

<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Object;

class Template {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $path;
	public $extensions = [ 'html' ];



	/*************************************************************************
	  CONSTRUCTOR             
	 *************************************************************************/
	public function by_path( $path ) {
		$this->path = $path;
		return $this;
	}

	public function by_name( $name ) {
		$name = strtolower( \Supersoniq\format_to_path( $name ) );
		$this->path = $this->get_template_path( $name );
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
	protected function get_template_path( $name ) {
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
		return SUPERSONIQ_ROOT_PATH . $design . '/' . $name . '.' . $extension;
	}
}

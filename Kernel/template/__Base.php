<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Template;

class __Base {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $parent;
	public $extensions = [ 'html', 'php' ];
	public $type;
	public $path;



	/*************************************************************************
	  GETTER                   
	 *************************************************************************/
	public function is_template_found( ) {
		return is_file( $this->path );
	}



	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {
		$this->type = \Supersoniq\class_type_name( $this );
		$this->path = $this->get_template_path( );
	}



	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
	public function __toString( ) {
		return $this->render( );
	}

	public function render( ) {
		$render_method = 'render_' . \Supersoniq\file_extension( $this->path );
		return $this->$render_method( );
	}

	protected function render_html( ) {
		return file_get_contents( $this->path );
	}

	protected function render_php( ) {
		ob_start();		
		require( $this->path );
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}




	/*************************************************************************
	  PARENT METHODS                   
	 *************************************************************************/
	public function compile( ) {
		if ( is_object( $this->parent ) ) {
			return $this->parent->compile( );
		}
		return $this;
	}
	protected function set_parent( $parent ) {
		$this->parent = $parent->get_template( );
		$this->parent->content = $this;
	}



	/*************************************************************************
	  PRIVATE TYPE METHODS                   
	 *************************************************************************/
	protected function is_module_template( ) {
		return \Supersoniq\starts_with( $this->type, 'Module\\' );
	}



	/*************************************************************************
	  PRIVATE FILE METHODS                   
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

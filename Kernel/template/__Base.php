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
	public $_parent;
	public $_extensions = [ ];
	public $_attributes = [ ];
	public $_type;
	public $_path;



	/*************************************************************************
	  GETTER & SETTER               
	 *************************************************************************/
	public function is_template_found( ) {
		return is_file( $this->_path );
	}

	public function __get( $name ) {
		return $this->get( $name );
	}

	public function get( $name ) {
		if ( ! \Supersoniq\starts_with( $name, '_' ) && isset( $this->_attributes[ $name ] ) ) {
			return $this->_attributes[ $name ];
		}
		return NULL;
	}

	public function __set( $name, $value ) {
		return $this->set( $name, $value );
	}

	public function set( $name, $value ) {
		if ( ! \Supersoniq\starts_with( $name, '_' ) ) {
			$this->_attributes[ $name ] = $value;
		}
		return $this;
	}



	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {
		$this->_type = \Supersoniq\class_type_name( $this );
		$this->_extensions = $this->get_extensions( );
		$this->_path = $this->get_template_path( );
		$this->content = '';
	}

	public function by_content( $content ) {
		$this->_path = FALSE;
		$this->content = $content;
		return $this;
	}



	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
	public function __toString( ) {
		return $this->render( );
	}

	public function render( ) {
		if ( ! $this->_path ) {
			return $this->display( $this->content );
		}
		$render_method = 'render_' . \Supersoniq\file_extension( $this->_path );
		return $this->$render_method( );
	}

	protected function render_html( ) {
		return file_get_contents( $this->_path );
	}

	protected function render_php( ) {
		ob_start();		
		require( $this->_path );
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	protected function get_extensions( ) {
		$extensions = [ ];
		foreach( get_class_methods( $this ) as $method ) {
			if ( \Supersoniq\starts_with( $method, 'render_' ) ) {
				$extensions[ ] = \Supersoniq\substr_after( $method, 'render_' );
			}
		}
		return $extensions;
	}
	
	protected function display( $var ) {
		if ( is_string( $var ) ) {
			return $var;
		}
		if ( is_object( $var ) ) {
			$type = \supersoniq\class_type( $var );
			if ( $type == 'Template' ) {
				return $var;
			}
			if ( $type == 'Model' ) {
				return ( new \Template )->by_model( $var )->render( );
			}
		}
	}



	/*************************************************************************
	  PARENT METHODS                   
	 *************************************************************************/
	public function compile( ) {
		if ( is_object( $this->_parent ) ) {
			return $this->_parent->compile( );
		}
		return $this;
	}
	protected function set_parent( $parent ) {
		$this->_parent = $parent->render( );
		$this->_parent->content = $this;
	}



	/*************************************************************************
	  PRIVATE TYPE METHODS                   
	 *************************************************************************/
	protected function is_module_template( ) {
		return \Supersoniq\starts_with( $this->_type, 'Module\\' );
	}



	/*************************************************************************
	  PRIVATE FILE METHODS                   
	 *************************************************************************/
	protected function get_template_path( ) {
		$name = \Supersoniq\format_to_path( strtolower( $this->_type ) );
		foreach ( \Supersoniq::$DESIGNS as $design ) {
			foreach ( $this->_extensions as $extension ) {
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

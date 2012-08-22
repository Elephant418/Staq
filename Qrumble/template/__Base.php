<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Qrumble\Template;

class __Base {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public $_type;
	public $_extensions = [ ];
	public $_attributes = [ ];
	public $_parent;
	public $_path = 'auto';



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
		if ( $this->_path == 'auto' ) {
			$this->_path = $this->get_template_path( );
		}
		if ( ! is_null( $this->_parent ) ) {
			$this->set_parent_view( ( new \View )->by_layout( $this->_parent ) );
		}
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
		$module_page_url = function( $module, $page, $parameter = NULL ) {
			$parameters = array_slice( func_get_args( ), 2 );
			return \Supersoniq\module_page_url( $module, $page, $parameters );
		};
		$page_url = function( $page, $parameter = NULL ) use ( $module_page_url ) {
			$parameters = array_slice( func_get_args( ), 1 );
			return \Supersoniq\module_page_url( \Supersoniq::$MODULE_NAME, $page, $parameters );
		};
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
	
	protected function display( $var, $mode = 'view' ) {
		if ( is_string( $var ) ) {
			return $var;
		}
		if ( is_object( $var ) ) {
			$type = \Supersoniq\class_type( $var );
			if ( $type == 'Template' ) {
				return $var->render( );
			}
			if ( $type == 'Model' ) {
				return ( new \Template )
					->by_model( $var, $mode )
					->set_parent( $this )
					->render( );
			}
			if ( $type == 'Data_Type' ) {
				return ( new \Template )
					->by_data_type( $var, $mode )
					->set_parent( $this )
					->render( );
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

	protected function set_parent_view( $parent ) {
		$this->set_parent( $parent->render( ) );
		$this->_parent->content = $this;
		return $this;
	}

	protected function set_parent( $parent ) {
		$this->_parent = $parent;
		return $this;
	}



	/*************************************************************************
	  PRIVATE FILE METHODS                   
	 *************************************************************************/
	protected function get_template_path( ) {
		$name = \Supersoniq\format_to_path( strtolower( $this->_type ) );
		do {
			foreach ( \Supersoniq::$EXTENSIONS as $extension ) {
				foreach ( $this->_extensions as $file_extension ) {
					$template_path = $this->get_template_path_by_extension( $name, $extension, $file_extension );
					if ( is_file( $template_path ) ) {
						return $template_path;
					}
				}
			}
			$name = \Supersoniq\substr_before_last( $name, '/' );
		} while ( $name );
		return FALSE;
	}

	protected function get_template_path_by_extension( $name, $extension, $file_extension ) {
		return SUPERSONIQ_ROOT_PATH . $extension . '/template/' . $name . '.tpl.' . $file_extension;
	}
}

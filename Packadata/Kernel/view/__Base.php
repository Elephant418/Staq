<?php

namespace Supersoniq\Packadata\Kernel\View;

class __Base {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $autoload_create_child = 'View\\__Base';
	const LAYOUT_TEMPLATE = 'layout.html';
	const VIEW_MODEL_TEMPLATE = 'view_model.html';
	const EDIT_MODEL_TEMPLATE = 'edit_model.html';
	const LIST_MODEL_TEMPLATE = 'list_model.html';
	const ARCHIVE_MODEL_TEMPLATE = 'archive_model.html';

	protected $_attributes = array( );


	/*************************************************************************
	  GETTER & SETTER             
	 *************************************************************************/
	public function __get( $name ) {
		return $this->get( $name );
	}
	public function get( $name ) {
		if ( ! isset( $this->_attributes[ $name ] ) ) {
			return NULL;
		}
		return $this->_attributes[ $name ];
	}
	public function __set( $name, $value ) {
		return $this->set( $name, $value );
	}
	public function set( $name, $value ) {
		$this->_attributes[ $name ] = $value;
	}


	/*************************************************************************
	  PUBLIC METHODS                   
	 *************************************************************************/
	public function render( $template_name ) {
		$template_path = $this->find_template( $template_name );
		ob_start();		
		require( $template_path );
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}


	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function find_template( $template_name ) {
		foreach ( \Supersoniq\Application::$root_paths as $root_path ) {
			$template_path = $root_path . 'view/' . $template_name;
			if ( file_exists( $template_path ) ) {
				return $template_path;
			}
		}
		throw new \Exception( 'Unknown template name "' . $template_name . '"' );
	}
}

<?php

namespace Supersoniq\Kernel\View;

class __Base {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $autoload_create_child = 'View\\__Base';
	const LAYOUT_TEMPLATE = 'layout.html';
	const VIEW_MODEL_TEMPLATE = 'view_model.html';
	const EDIT_MODEL_TEMPLATE = 'edit_model.html';
	const LIST_MODEL_TEMPLATE = 'list_model.html';
	const LIST_ARCHIVE_TEMPLATE = 'archive/list_archive.html';
	const VIEW_ARCHIVE_TEMPLATE = 'archive/view_archive.html';
	const DELETED_MODELS_TEMPLATE = 'archive/deleted_models.html';

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
		$controller_action_url = function( $controller, $action, $parameter = NULL ) {
			$parameters = array_slice( func_get_args( ), 2 );
			return \Supersoniq\Application::action_url( $controller, $action, $parameters );
		};
		$action_url = function( $action, $parameter = NULL ) {
			$parameters = array_slice( func_get_args( ), 1 );
			return \Supersoniq\Application::action_url( \Supersoniq\Application::$current_controller, $action, $parameters );
		};
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
		foreach ( \Supersoniq\Application::$modules_path as $module_path ) {
			$template_path = $module_path . 'view/' . $template_name;
			if ( file_exists( $template_path ) ) {
				return $template_path;
			}
		}
		throw new \Exception( 'Unknown template name "' . $template_name . '"' );
	}
}

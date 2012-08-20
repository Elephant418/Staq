<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Module;

abstract class Model extends \__Auto\Module\__Base {



	/*************************************************************************
	  GETTER                 
	 *************************************************************************/
	public function name( ) {
		return basename( \Supersoniq\format_to_path( $this->type ) );
	}



	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function get_page_view( $page ) {
		$module = 'Model\\';
		foreach ( explode( '_', $page ) as $part ) {
			$module .= ucfirst( $part ) . '_';
		}
		$module = substr( $module, 0, -1 );
		return ( new \View )->by_module_page( $module, $this->get_model_name( ) );
	}

	protected function get_model_name( ) {
		return \Supersoniq\substr_after( $this->type, '\\' );
	}

	protected function get_route( $page ) {
		return parent::get_route( $page )->add_prefix( strtolower( $this->get_model_name( ) ) );
	}

}

<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Qrumble\Module;

abstract class __Base extends __Base\__Parent {



	/*************************************************************************
	  ROUTE METHODS                   
	 *************************************************************************/
	public function call_page( $page, $parameters ) {
		if ( ! is_callable( [ $this, $page ] ) ) {
			$template =  $this->get_page_view( $page )->render( $parameters );
		} else {
			$template = call_user_func_array( [ $this, $page ], $parameters );
		}
		if ( \Supersoniq\class_type( $template ) != 'Template' ) {
			$template = ( new \Template )
				->by_module_page( $this, $page )
				->by_content( $template );
		}
		return $template;
	}



	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function get_page_view( $page ) {
		return ( new \View )->by_module_page( $this, $page );
	}

}


<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Module;

class Page {



	/*************************************************************************
	  ATTRIBUTES				   
	 *************************************************************************/
	public $type = 'Page';



	/*************************************************************************
	  ROUTE METHODS				   
	 *************************************************************************/
	public function handle_route( $route ) {
		if ( \Supersoniq\ends_with( $route, '/')  ) {
			$route .= '/index';
		}
		$path = '/page' . $route . '.html';
		foreach ( \Supersoniq::$EXTENSIONS as $extension ) {
			$file_path = SUPERSONIQ_ROOT_PATH . $extension . $path;
			if ( is_file( $file_path ) ) {
				return [ 'page', [ $file_path ] ];
			}
		}
		return FALSE;
	}

	public function handle_exception( $exception ) {
		return FALSE;
	}

	public function call_page( $page, $parameters ) {
		return call_user_func_array( [ $this, $page ], $parameters );
	}



	/*************************************************************************
	  MENU METHODS                   
	 *************************************************************************/
	public function get_menu( $name ) {
		return [ ];
	}



	/*************************************************************************
	  SIDE METHODS				   
	 *************************************************************************/
	public function page( $file_path ) {
		$html = file_get_contents( $file_path );
		$template = ( new \Template )
			->by_module_page( $this, 'page' )
			->by_content( $html );
		return $template;
	}
}

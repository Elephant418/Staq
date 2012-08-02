<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Qrumble\View;

class Layout extends Layout\__Parent {



	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function fill( $template, $parameters = [ ] ) {
		$template->application_name = \Supersoniq::$APPLICATION_NAME;
		
		// Main Menu		
		$menu_main = [ ];
		foreach( \Supersoniq::$MODULES as $module ) {
			$menu_main = array_merge_recursive( $menu_main, $module->get_menu( 'main' ) );
		}
		$template->menu_main = array_reverse( $menu_main );
		
		// Session Menu		
		$menu_session = [ ];
		foreach( \Supersoniq::$MODULES as $module ) {
			$menu_session = array_merge_recursive( $menu_session, $module->get_menu( 'session' ) );
		}
		$template->menu_session = array_reverse( $menu_session );
		
		return $template;
	}
}

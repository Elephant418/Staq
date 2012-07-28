<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\View;

class Layout extends Layout\__Parent {



	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function fill( $template ) {
		$template->application_name = \Supersoniq::$APPLICATION_NAME;
		
		// Main Menu		
		$menu_main = [ ];
		foreach( \Supersoniq::$MODULES as $module ) {
			$menu = $module->get_menu( 'main' );
			if ( ! empty( $menu ) ) {
				$menu_main[ $module->type ] = $menu;
			}
		}
		$template->menu_main = $menu_main;
		
		return $template;
	}
}

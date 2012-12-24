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
	  MENU METHODS                   
	 *************************************************************************/
	public function get_menu( $name ) {
		$menu = [ ];
		$settings = $this->settings->get_array( 'menu_' . $name );
		foreach ( $settings as $key => $infos ) {
			if ( isset( $infos[ 'page' ] ) ) {
				$page = $infos[ 'page' ];
			} else {
				$page = $key;
			}
			if ( $this->is_route_page( $page ) && ! empty( $infos ) ) {
				$menu[ $key ] = [ ]; 
				if ( ! is_array( $infos ) ) {
					$menu[ $key ][ 'label' ] = $infos;
				} else if ( isset( $infos[ 'label' ] ) ) {
					$menu[ $key ][ 'label' ] = $infos[ 'label' ];
				} else {
					$menu[ $key ][ 'label' ] = $key;
				}
				if ( isset( $infos[ 'description' ] ) ) {
					$menu[ $key ][ 'description' ] = $infos[ 'description' ];
				} else {
					$menu[ $key ][ 'description' ] = $this->name( ) . ' > ' . $menu[ $key ][ 'label' ];
				}
				if ( isset( $infos[ 'parameters' ] ) ) {
					$parameters = explode( ',', $infos[ 'parameters' ] );
				} else {
					$parameters = [ ];
				}
				$menu[ $key ][ 'url' ] = $this->get_page_url( $page, $parameters );
			}
		}
		if ( ! empty( $menu ) ) {
			return [ $this->name( ) => $menu ];
		}
		return [ ];
	}



	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
	protected function get_page_view( $page ) {
		return ( new \View )->by_module_page( $this, $page );
	}

}


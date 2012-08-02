<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Authent\Module;

class Authent extends \__Auto\Module\__Base {



	/*************************************************************************
	  GETTER                 
	 *************************************************************************/
	public function name( ) {
		return 'Account';
	}



	/*************************************************************************
	  MENU METHODS                   
	 *************************************************************************/
	public function get_menu( $name ) {
		$menu = [ ];
		if ( $name == 'session' ) {
			$controller = ( new \Controller )->by_type( 'Model\User' );
			if ( $controller->is_logged( ) ) {
				$user = $controller->current_user( );
				$menu[ $user->name( ) ] = [
					'profile' => [
						'label'       => 'Profile',
						'description' => 'Profile',
						'url'         => $this->get_page_url( 'profile' )
					],
					'logout' => [
						'label'       => 'Logout',
						'description' => 'Logout',
						'url'         => $this->get_page_url( 'logout' )
					],
				];
			} else {
				$menu[ 'Account' ][ 'login' ] = [
					'label'       => 'Login',
					'description' => 'Login',
					'url'         => $this->get_page_url( 'login' )
				];
			}
		}
		return $menu;
	}

}

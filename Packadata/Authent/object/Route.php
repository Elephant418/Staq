<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Authent\Object;

class Route extends Route\__Parent {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected function restriction_user_right( $right ) {
		$user = ( new \Controller )->by_type( 'Model\User' )->current_user( );
		if ( $user ) {
			return ( $user->right == $right );
		}
	}
}


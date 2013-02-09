<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack\Model;

class User extends User\__Parent {

	public function by_login( $login ) {
		return $this->by_field( 'login', $login );
	}
}

<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type;

class MailAddress extends \Data_Type\__Base {

	
	/*************************************************************************
	 USER GETTER & SETTER
	*************************************************************************/
	public function set( $value ) {
		if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
			$this->init( $value );
		} else {
			\Notification::push( 'Wrong Input for the mail ! Hasn\'t been saved.', \Notification::ERROR );
		}
	}
}

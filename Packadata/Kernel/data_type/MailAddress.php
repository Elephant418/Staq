<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type;

class MailAddress extends \Data_Type\__Base {


	/*************************************************************************
	 ATTRIBUTES
	*************************************************************************/
	/* Regex from SdZ
	 * Mail validity verification
	 */
	private $regex="#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#";

	
	/*************************************************************************
	 USER GETTER & SETTER
	*************************************************************************/
	public function set( $value ) {
		if ( preg_match( $this->regex, $value ) || $value == '' ) {
			$this->init( $value );
		} else {
			\Notification::push( 'Wrong Input for the mail ! Hasn\'t been saved.', \Notification::NOTICE );
			//TODO Interrompre totalement l'entrée en base
		}
	}
}

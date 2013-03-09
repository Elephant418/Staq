<?php

/* This file is part of the Ubiq project, which is under MIT license */

namespace Staq\Core\View\Stack\Util;

class UINotification {



	/*************************************************************************
	  CONSTANTS				   
	 *************************************************************************/
	const INFO = 'info';
	const SUCCESS = 'success';
	const ERROR = 'error';



	/*************************************************************************
	  CONSTRUCTOR METHODS				   
	 *************************************************************************/
	public function __construct( $message, $type = 'info' ) {
		$info = array( 'message' => $message, 'type' => $type );
		if ( ! isset( $_SESSION[ 'Staq' ][ 'UINotification' ] ) ) {
			$_SESSION[ 'Staq' ][ 'UINotification' ] = array( );
		}
		$_SESSION[ 'Staq' ][ 'UINotification' ][ ] = $info;
	}



	/*************************************************************************
	  STATIC METHODS				   
	 *************************************************************************/
	public static function pull( ) {
		$messages = array( );
		if ( isset( $_SESSION[ 'Staq' ][ 'UINotification' ] ) ) {
			$messages = $_SESSION[ 'Staq' ][ 'UINotification' ];
			$_SESSION[ 'Staq' ][ 'UINotification' ] = array( );
		}
		return $messages;
	}
}
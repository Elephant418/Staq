<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util\Auth\Stack\Attribute ;

class Password extends Password\__Parent {


    /*************************************************************************
    ATTRIBUTES
     *************************************************************************/
    const CRYPT_SEED = 'dacz:;,aafapojn';



	/*************************************************************************
	  PUBLIC USER METHODS             
	 *************************************************************************/
	public function get( ) {
		return '';
	}

	public function set( $value ) {
        $this->seed = $this->encryptPassword($value);
	}

	public function compare( $password ) {
        $password = $this->encryptPassword($password);
        return ( $this->seed === $password );
	}



    /*************************************************************************
    PRIVATE METHODS
     *************************************************************************/
    protected function encryptPassword($password)
    {
        return sha1(static::CRYPT_SEED . $password);
    }
}
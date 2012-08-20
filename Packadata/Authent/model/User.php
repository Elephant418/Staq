<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Authent\Model;

class User extends \Model\Defined {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	const CRYPT_SEED = 'dacz:;,aafapojn';


	/*************************************************************************
	  GETTER & SETTER             
	 *************************************************************************/
	public function name( ) {
		return $this->name . ' ' . $this->lastname;
	}


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {
		parent::__construct( );
		$this->add_attribute( 'login'   , new \Data_Type\Varchar, \Model_Index::UNIQUE );
		$this->add_attribute( 'email'   , new \Data_Type\Varchar, \Model_Index::UNIQUE );
		$this->add_attribute( 'password', new \Data_Type\Password );
		$this->add_attribute( 'name' );
		$this->add_attribute( 'lastname' );
		$this->add_attribute( 'right'  , new \Data_Type\Selection( [
			10 => 'visitor',
			20 => 'admin'
		] ) );
	}


	/*************************************************************************
	  INITIALIZATION          
	 *************************************************************************/
	public function by_login( $login ) {
		return $this->by_index( 'login', $login );
	}

	public function by_email( $email ) {
		return $this->by_index( 'email', $email );
	}


	/*************************************************************************
	  PULIC METHODS          
	 *************************************************************************/
	public function check_password( $password ) {
		return ( $this->attribute( 'password' )->value( ) === $this->encrypt_password( $password ) );
	}

	public function encrypt_password( $password ) {
		return sha1( self::CRYPT_SEED . $password );
	}
}



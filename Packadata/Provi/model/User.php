<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Provi\Model;

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
		$this->add_attribute( 'login', new \Data_Type\Varchar( ), \Model_Index::UNIQUE );
		$this->add_attribute( 'email', new \Data_Type\Varchar( ), \Model_Index::UNIQUE );
		$this->add_attribute( 'activate', new \Data_Type\Boolean( ) );
		$this->add_attribute( 'activation_code' );
		$this->add_attribute( 'password' );
		$this->add_attribute( 'name' );
		$this->add_attribute( 'lastname' );
	}


	/*************************************************************************
	  INITIALIZATION          
	 *************************************************************************/
	public function init_by_login( $login ) {
		return $this->init_by_index( 'login', $login );
	}
	public function init_by_email( $email ) {
		return $this->init_by_index( 'email', $email );
	}


	/*************************************************************************
	  PULIC METHODS          
	 *************************************************************************/
	public function check_password( $password ) {
		return ( $this->password === $this->encrypt_password( $password ) );
	}
	public function encrypt_password( $password ) {
		return sha1( self::CRYPT_SEED . $password );
	}
}



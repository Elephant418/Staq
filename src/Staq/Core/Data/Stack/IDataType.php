<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack ;

interface IDataType {



	/*************************************************************************
	  PUBLIC USER METHODS             
	 *************************************************************************/
	public function get( );

	public function set( $value );



	/*************************************************************************
	  PUBLIC DATABASE METHODS             
	 *************************************************************************/
	public function get_seed( );

	public function set_seed( $seed );
}
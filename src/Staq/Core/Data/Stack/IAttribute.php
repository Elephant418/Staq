<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack ;

interface IAttribute {



	/*************************************************************************
	  PUBLIC USER METHODS             
	 *************************************************************************/
	public function get( );

	public function set( $value );



	/*************************************************************************
	  PUBLIC DATABASE METHODS             
	 *************************************************************************/
	public function getSeed( );

	public function setSeed( $seed );
}
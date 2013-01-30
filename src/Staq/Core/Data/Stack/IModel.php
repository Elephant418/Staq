<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack;

interface IModel {


	/*************************************************************************
	  GETTER                 
	 *************************************************************************/
	public function exists( );


	/*************************************************************************
	  INITIALIZATION          
	 *************************************************************************/
	public function by_data( $data );

	public function by_id( $id );

	public function all( );


	/*************************************************************************
	  PUBLIC DATABASE REQUEST
	 *************************************************************************/
	public function delete( );

	public function save( );

	public function extract_seeds( );


	/*************************************************************************
	  SPECIFIC MODEL ACCESSOR METHODS				   
	 *************************************************************************/
	public function get_attribute( $index );

	public function attribute_names( );
}

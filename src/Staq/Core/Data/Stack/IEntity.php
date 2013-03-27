<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack;

interface IEntity {


	/*************************************************************************
	  FETCHING METHODS          
	 *************************************************************************/
	public function extractId( &$data );

	public function getDataById( $id );

	public function getDataByFields( $fields = [ ] );

	public function getDatasByFields( $fields = [ ] );

	public function deleteByFields( $fields );


	/*************************************************************************
	  PUBLIC DATABASE REQUEST
	 *************************************************************************/
	public function delete( $model );

	public function save( $model );
}

<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack;

interface IEntity {


	/*************************************************************************
	  FETCHING METHODS          
	 *************************************************************************/
	public function extract_id( $data );

	public function get_data_by_id( $id );

	public function get_data_by_fields( $fields = [ ] );

	public function get_datas_by_fields( $fields = [ ] );

	public function delete_by_fields( $fields );


	/*************************************************************************
	  PUBLIC DATABASE REQUEST
	 *************************************************************************/
	public function delete( $model );

	public function save( $model );
}

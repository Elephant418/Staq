<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Controller;

abstract class Model extends Model\__Parent {



	/*************************************************************************
	  ACTION METHODS                   
	 *************************************************************************/
	public function all( ) {
		$model = $this->model( );
		return $model->all( ); 
	}
	public function get( $id = NULL ) {
		$model = $this->model( );
		if ( ! $id ) {
			return $model;
		}
		if ( $model->init_by_id( $id ) ) {
			return $model;
		}
		return FALSE;
	}
	public function edit( &$model, $datas ) {
		foreach ( $datas as $name => $value ) {
			$model->$name = $value;
		}
		if ( $model->save( ) ) {
			\Notification::push( $this->get_model_name( ) . ' updated with success ! ', \Notification::SUCCESS );
			return TRUE;
		}
		\Notification::push( $this->get_model_name( ) . ' not updated !', \Notification::ERROR );
		return FALSE;
	}
	public function delete( $model ) {
		$model->delete( );
		\Notification::push( $this->get_model_name( ) . ' deleted with success ! ', \Notification::SUCCESS );
		return TRUE;
	}


	/*************************************************************************
	  PROTECTED METHODS                   
	 *************************************************************************/
	protected function get_model_name( ) {
		return \Supersoniq\substr_after( $this->type, '\\' );
	}

	protected function model( ) {
		return ( new \Model )->by_name( $this->get_model_name( ) );
	}
}

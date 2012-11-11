<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Controller;

abstract class Model_Unversioned extends Model\__Parent {


	/*************************************************************************
	  GETTER METHODS                   
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
		$model = $model->by_id( $id );
		if ( $model->exists( ) ) {
			return $model;
		}
		return FALSE;
	}

	public function get_subtype( $id = NULL ) {
		return ( new \Settings )
			->by_type( $this->type )
			->get_list( 'subtypes' );
	}


	/*************************************************************************
	  ACTION METHODS                   
	 *************************************************************************/
	public function edit( &$model, $datas, $message = TRUE ) {
		foreach ( $datas as $name => $value ) {
			if ( $model->has_attribute( $name ) ) {
				$model->$name = $value;
			}
		}
		$exists = $model->exists( );
		if ( $model->save( ) ) {
			if ( $message ) {
				if ( $exists ) {
					$message = $this->get_model_name( ) . ' updated with success ! ';
				} else {
					$message = $this->get_model_name( ) . ' created with success ! ';
				}
				\Notification::push( $message, \Notification::SUCCESS );
			}
			return TRUE;
		}
		if ( $message ) {
			if ( $exists ) {
				$message = $this->get_model_name( ) . ' not updated ! ';
			} else {
				$message = $this->get_model_name( ) . ' not created ! ';
			}
			\Notification::push( $message, \Notification::ERROR );
		}
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
		return ( new \Model )->by_type( $this->get_model_name( ) );
	}
}

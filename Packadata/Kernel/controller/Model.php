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
	public function get( $id ) {
		$model = $this->model( );
		if ( $model->init_by_id( $id ) ) {
			return $model;
		}
		return FALSE;
	}
	public function create( ) {
		$model = $this->model( );
		if ( isset( $_POST[ 'model' ] ) ) {
			foreach ( $_POST[ 'model' ] as $name => $value ) {
				$model->$name = $value;
			}
			$model->save( );
			if ( $model->exists( ) ) {
				\Notification::push( $this->type . ' created with success ! ', \Notification::SUCCESS );
				\Supersoniq\Application::redirect_to_action( $this->type, 'view', array( 'id' => $model->id ) );
			}
			\Notification::push( $this->type . ' not created !', \Notification::ERROR );
		}
		return $model; 
	}
	public function edit( $id ) {
		$model = $this->model( );
		if ( $model->init_by_id( $id ) ) {
			if ( isset( $_POST[ 'model' ] ) ) {
				foreach ( $_POST[ 'model' ] as $name => $value ) {
					$model->$name = $value;
				}
				if ( $model->save( ) ) {
					\Notification::push( $this->type . ' updated with success ! ', \Notification::SUCCESS );
					\Supersoniq\Application::redirect_to_action( $this->type, 'view', array( 'id' => $model->id ) );
				}
				\Notification::push( $this->type . ' not updated !', \Notification::ERROR );
			}
		}
		return $model; 
	}
	public function delete( $id ) {
		$model = $this->model( );
		if ( $model->init_by_id( $id ) ) {
			$model->delete( );
			\Notification::push( $this->type . ' deleted with success ! ', \Notification::SUCCESS );
			return TRUE;
		}
		return FALSE;
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

<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute\Relation;

class ManyToOne extends ManyToOne\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $remote_model;
	protected $remote_class_type;



	/*************************************************************************
	  CONSTRUCTOR            
	 *************************************************************************/
	public function init_bySetting( $model, $setting ) {
		if ( is_array( $setting ) ) {
			if ( isset( $setting[ 'remote_class_type' ] ) ) {
				$this->remote_class_type = $setting[ 'remote_class_type' ];
			} 
		}
	}



	/*************************************************************************
	  PUBLIC USER METHODS             
	 *************************************************************************/
	public function get( ) {
		if ( is_null( $this->remote_model ) && isset( $this->seed ) ) {
			$class = $this->get_remote_class( );
			$this->remote_model = ( new $class )->by_id( $this->seed );
		}
		return $this->remote_model;
	}

	public function set( $model ) {
		if ( ! \Staq\Util::isStack( $model, $this->get_remote_class( ) ) ) {
			$message = 'Input of type "' . $this->get_remote_class( ) . '", but "' . gettype( $model ) . '" given.';
			throw new \Stack\Exception\NotRightInput( $message );
		}
		if ( ! $model->exists( ) ) {
			$this->seed = NULL;
			$this->remote_model = NULL;
		} else {
			$this->remote_model = $model;
			$this->seed = $model->id;
		}
	}



	/*************************************************************************
	  PUBLIC DATABASE METHODS             
	 *************************************************************************/
	public function get_seed( ) {
		return $this->seed;
	}

	public function set_seed( $seed ) {
		$this->seed = $seed;
		$this->remote_model = NULL;
	}



	/*************************************************************************
	  PROTECTED METHODS             
	 *************************************************************************/
	protected function get_remote_class( ) {
		return $class = 'Stack\\Model\\' . $this->remote_class_type;
	}	
}
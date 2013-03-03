<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute\Relation;

class ManyToOne extends ManyToOne\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $remoteModel;
	protected $remoteClassType;



	/*************************************************************************
	  CONSTRUCTOR            
	 *************************************************************************/
	public function initBySetting( $model, $setting ) {
		if ( is_array( $setting ) ) {
			if ( isset( $setting[ 'remote_class_type' ] ) ) {
				$this->remoteClassType = $setting[ 'remote_class_type' ];
			} 
		}
	}



	/*************************************************************************
	  PUBLIC USER METHODS             
	 *************************************************************************/
	public function get( ) {
		if ( is_null( $this->remoteModel ) && isset( $this->seed ) ) {
			$class = $this->getRemoteClass( );
			$this->remoteModel = ( new $class )->byId( $this->seed );
		}
		return $this->remoteModel;
	}

	public function set( $model ) {
		if ( ! \Staq\Util::isStack( $model, $this->getRemoteClass( ) ) ) {
			$message = 'Input of type "' . $this->getRemoteClass( ) . '", but "' . gettype( $model ) . '" given.';
			throw new \Stack\Exception\NotRightInput( $message );
		}
		if ( ! $model->exists( ) ) {
			$this->seed = NULL;
			$this->remoteModel = NULL;
		} else {
			$this->remoteModel = $model;
			$this->seed = $model->id;
		}
	}



	/*************************************************************************
	  PUBLIC DATABASE METHODS             
	 *************************************************************************/
	public function getSeed( ) {
		return $this->seed;
	}

	public function setSeed( $seed ) {
		$this->seed = $seed;
		$this->remoteModel = NULL;
	}



	/*************************************************************************
	  PROTECTED METHODS             
	 *************************************************************************/
	protected function getRemoteClass( ) {
		return $class = 'Stack\\Model\\' . $this->remoteClassType;
	}	
}
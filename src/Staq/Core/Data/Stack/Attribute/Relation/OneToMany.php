<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute\Relation;

class OneToMany extends OneToMany\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $model;
	protected $remoteModels = NULL;
	protected $remoteModelType;
	protected $remoteAttributeName;



	/*************************************************************************
	  CONSTRUCTOR            
	 *************************************************************************/
	public function initBySetting( $model, $setting ) {
		$this->model = $model;
		if ( is_array( $setting ) ) {
			if ( ! isset( $setting[ 'remote_class_type' ] ) ) {
				throw new \Stack\Exception\MissingSetting( '"remote_class_type" missing for the OneToMany relation.');
			} 
			if ( ! isset( $setting[ 'remote_attribute_name' ] ) ) {
				throw new \Stack\Exception\MissingSetting( '"remote_attribute_name" missing for the OneToMany relation.');
			} 
			$this->remoteModelType = $setting[ 'remote_class_type' ];
			$this->remoteAttributeName = $setting[ 'remote_attribute_name' ];
		}
	}



	/*************************************************************************
	  PUBLIC USER METHODS             
	 *************************************************************************/
	public function get( ) {
		if ( is_null( $this->remoteModels ) ) {
			$request = [ $this->remoteAttributeName => $this->model->id ];
			$class = $this->getRemoteClass( );
			$this->remoteModels = ( new $class )->fetch( $request );
		}
		return $this->remoteModels;
	}

	public function set( $remoteModels ) {
		// TODO: Manage to keep old ones to delete it.
		return $this->remoteModels = $remoteModels;
	}



	/*************************************************************************
	  PUBLIC DATABASE METHODS             
	 *************************************************************************/
	public function getSeed( ) {
		return NULL;
	}

	public function setSeed( $seed ) {
	}



    /*************************************************************************
    PUBLIC METHODS
     *************************************************************************/
    public function getRelatedModels( ) {
        $class = $this->getRemoteClass( );
        return ( new $class )->all( );
    }



    /*************************************************************************
    PUBLIC METHODS
     *************************************************************************/
    public function getRemoteModel( ) {
        $class = $this->getRemoteClass( );
        return new $class;
    }

    public function getRemoteModelType( ) {
        return $this->remoteModelType;
    }

    public function getRemoteClass( ) {
        return $class = 'Stack\\Model\\' . $this->remoteModelType;
    }
}
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
	public function get( $request = [], $limit = NULL, $order = NULL ) {
        $class = $this->getRemoteClass( );
        return ( new $class )->entity->fetchByField( $this->remoteAttributeName, $this->model->id, $limit, $order );
	}

    public function getIds( ) {
        $ids = [];
        foreach ( $this->get( ) as $model ) {
            $ids[] = $model->id;
        }
        return $ids;
    }

	public function set( $remoteModels ) {
		// Do nothing here :'(
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
        return ( new $class )->fetchAll( );
    }

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
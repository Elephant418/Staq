<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Data\Stack\Attribute\Relation;

class OneToMany extends OneToMany\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $model;
	protected $remote_models = NULL;
	protected $remote_class_type;
	protected $remote_attribute_name;



	/*************************************************************************
	  CONSTRUCTOR            
	 *************************************************************************/
	public function init_by_setting( $model, $setting ) {
		$this->model = $model;
		if ( is_array( $setting ) ) {
			if ( ! isset( $setting[ 'remote_class_type' ] ) ) {
				throw new \Stack\Exception\MissingSetting( '"remote_class_type" missing for the OneToMany relation.');
			} 
			if ( ! isset( $setting[ 'remote_attribute_name' ] ) ) {
				throw new \Stack\Exception\MissingSetting( '"remote_attribute_name" missing for the OneToMany relation.');
			} 
			$this->remote_class_type = $setting[ 'remote_class_type' ];
			$this->remote_attribute_name = $setting[ 'remote_attribute_name' ];
		}
	}



	/*************************************************************************
	  PUBLIC USER METHODS             
	 *************************************************************************/
	public function get( ) {
		if ( is_null( $this->remote_models ) ) {
			$request = [ $this->remote_attribute_name => $this->model->id ];
			$class = $this->get_remote_class( );
			$this->remote_models = ( new $class )->fetch( $request );
		}
		return $this->remote_models;
	}

	public function set( $remote_models ) {
		// TODO: Manage to keep old ones to delete it.
		return $this->remote_models = $remote_models;
	}



	/*************************************************************************
	  PUBLIC DATABASE METHODS             
	 *************************************************************************/
	public function get_seed( ) {
		return NULL;
	}

	public function set_seed( $seed ) {
	}



	/*************************************************************************
	  PROTECTED METHODS             
	 *************************************************************************/
	protected function get_remote_class( ) {
		return $class = 'Stack\\Model\\' . $this->remote_class_type;
	}	
}
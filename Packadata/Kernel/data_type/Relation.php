<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type;

class Relation extends \Data_Type\__Base {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	protected $initialized = FALSE;
	protected $definition;
	protected $relations = array( );


	/*************************************************************************
	  USER GETTER & SETTER             
	 *************************************************************************/
	public function get( ) {
		if ( ! $this->initialized ) {
			$this->relations = $this->definition->all( );
			$this->initialized = TRUE;
		}
		$relateds = array( );
		foreach ( $this->relations as $relation ) {
			$relateds[ ] = $relation->get( );
		}
		return $relateds;
	}
	public function set( $relateds ) {
		if ( ! is_array( $relateds ) ) {
			$relateds = array( $relateds );
		}
		$this->relations = array( );
		foreach ( $relateds as $related ) {
			$this->definition->set( $related );
			$this->relations[ ] = clone $this->definition;
		}
	}


	/*************************************************************************
	  DATABASE GETTER & SETTER             
	 *************************************************************************/
	public function value( ) {
		return NULL;
	}
	public function init( $value ) {
	}


	/*************************************************************************
	  MODEL EVENT METHODS             
	 *************************************************************************/
	public function model_initialized( $model ) {
		$this->definition->set_model( $model );
	}
	public function model_saved( $model ) {
		$this->definition->set_model( $model );
		// TODO: delete removed relateds
		foreach ( $this->relations as $relation ) {
			$relation->save( );
		}
	}
	public function model_deleted( $model ) {
		foreach ( $this->relations as $relation ) {
			$relation->delete( );
		}
	}


	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $definition ) {
		parent::__construct( );
		$this->definition = $definition;
	}
}

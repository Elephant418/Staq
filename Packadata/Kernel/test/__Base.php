<?php

namespace Supersoniq\Packadata\Kernel\Test;

class __Base {


	/*************************************************************************
	 ATTRIBUTES
	*************************************************************************/
	public $content;
	protected $verbose = TRUE;


	/*************************************************************************
	 GETTER & SETTER
	*************************************************************************/


	/*************************************************************************
	 CONSTRUCTOR
	*************************************************************************/
	public function __construct( $model ) {
		$this->content = $this->execute_script( $model );
	}


	/*************************************************************************
	 SCRIPTS
	*************************************************************************/	
	public function execute_script( $model ) {
		
		/* 
		 * TODO Initialisation to make somewhere else ?
		 */
		$object = $this->new_object( $model );
		$index = $object->get_attribute_fields( );
		$index = $index[0];
		
		$content = '<h2>Script results</h2>';
		/* Creation Test */
		$obj1 = $this->new_object( $model, array( $index => $model . '1V' . $object->type_version . '.1' ) );
		$content .= 'Create (must be OK) : ' . $this->save( $obj1 ) . '<br/>';
		$obj2 = $this->new_object( $model, array( $index => $model . '2V' . $object->type_version . '.1' ) );
		$content .= 'Create (must be OK) : ' . $this->save( $obj2 ) . '<br/>';
		/* Edition Test */
		$id1 = $obj1->id;
		$obj11 = $this->get_object( $model, $obj1->id );
		$content .= 'Update (must be OK, but without archiving) : ' . $this->save( $obj11 ) . '<br/>';
		$obj12 = $this->get_object( $model, $obj1->id );
		$obj12->set( $index, $model . '1V' . $object->type_version . '.2' );
		$content .= 'Update  (must be OK) : ' . $this->save( $obj12 ) . '<br/>';
		/* Suppression Test */
		$obj3 = $this->new_object( $model, array( $index => $model . '3V' . $object->type_version . '.1' ) );
		$content .= 'Create (must be OK) : ' . $this->save( $obj3 ) . '<br/>';
		$obj32 = $this->get_object( $model, $obj3->id );
		$obj32->set( $index, $model . '3V' . $object->type_version . '.2' );
		$content .= 'Update  (must be OK) : ' . $this->save( $obj32 ) . '<br/>';
		$obj3d = $this->get_object( $model, $obj3->id );
		$content .= 'Delete (must be void) : ' . $this->delete( $obj3d ) . '<br/>';
		/* Model Version Test */
		$obj122 = $this->get_object( $model, $obj1->id );
		$obj122->type_version ++;
		$obj122->set( $index, $model . '1V' . $obj122->type_version . '.3' );
		$content .= 'Update  (must be OK) : ' . $this->save( $obj122 ) . '<br/>';
		
		if ( $this->verbose ) {
			return $content;
		}
	}


	/*************************************************************************
	 METHODS
	*************************************************************************/
	/*
	 * Creates a new object and initializes it
	 */
	public function new_object( $type, $fields = NULL ) {
		$model_type = '\Model\\' . $type;
		$object = new $model_type;
		if ( $fields != NULL ) {
			foreach ( $fields as $key => $value ) {
				$object->set( $key, $value );
			}
		}
		return $object;
	}
	/*
	 * Gets an existing object
	 */
	public function get_object( $type, $id ) {
		$model_type = '\Model\\' . $type;
		$object = ( new $model_type )->by_id( $id );
		return $object;
	}
	/*
	 * Save an object in the database
	 */
	public function save( $object ) {
		return $object->save( );
	}
	/*
	 * Delete an object from the database
	*/
	public function delete( $object ) {
		return $object->delete( );
	}
}

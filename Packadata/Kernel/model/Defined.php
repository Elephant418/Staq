<?php

namespace Supersoniq\Packadata\Kernel\Model;

class Defined extends \Model\__Base {



	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	protected $_attributes_alias = [ ];
	protected $_indexs = [ ];
	protected $_uniqs = [ ];



	/*************************************************************************
	  GETTER & SETTER             
	 *************************************************************************/
	public function get( $name ) {
		if ( isset( $this->_attributes[ $name ] ) ) {
			return $this->_attributes[ $name ]->get( );
		} 
		if ( isset( $this->_attributes_alias[ $name ] ) ) {
			return $this->_attributes_alias[ $name ]( $this );
		} 
	}

	public function set( $name, $value ) {
		if ( ! isset( $this->_attributes[ $name ] ) ) {
			throw new \Exception( 'Unexisting attribute "' . $name . '"' );
			return $this;
		}
		$this->_attributes[ $name ]->set( $value );
		return $this;
	}



	/*************************************************************************
	  ATTRIBUTES DEFINITION METHODS
	 *************************************************************************/
	protected function add_attribute( $name, $data_type = NULL, $constraint = FALSE ) {
		if ( ! is_object( $data_type ) ) {
			$data_type = new \Data_Type\Varchar( );
		} else if ( is_a( $data_type, '__Auto\Relation\__Base' ) ) {
			$data_type = new \Data_Type\Relation( $data_type );
		}
		$data_type->name = $name;
		// echo get_class( $data_type ) . PHP_EOL;
		if ( $constraint ) {
			$index = new \Model_Index( );
			$index->init( $this, $name );
			$this->_indexs[ $name ] = $index;
			if ( $constraint == \Model_Index::UNIQUE ) {
				$this->_uniqs[ $name ] = $index;
			}
		}
		$this->_attributes[ $name ] = $data_type;
	}

	protected function add_attribute_alias( $name, $alias ) {
		$this->_attributes_alias[ $name ] = $alias;
	}

	protected function remove_attribute( $name ) {
		unset( $this->_attributes[ $name ] );
	}



	/*************************************************************************
	  INITIALIZATION          
	 *************************************************************************/
	public function by_index( $type, $value ) {
		$index = new \Model_Index( );
		if ( $id = $index->model_id_by_value( $this->type, $type, $value ) ) {
			return $this->by_id( $id );
		}
		return $this;
	}

	

	/*************************************************************************
	  METHODS TO EXTEND
	 *************************************************************************/
	protected function simplify_index( $index ) {
		return preg_replace( '/[ -]+/', '-', preg_replace( '/[^a-zA-Z0-9- ]/', '', strtolower( $index ) ) );
	}



	/*************************************************************************
	  PUBLIC DATABASE REQUEST
	 *************************************************************************/
	public function delete( ) {
		foreach ( $this->get_attribute_fields( ) as $name ) {
			$this->_attributes[ $name ]->model_deleted( $this );
		}
		foreach ( $this->_indexs as $index ) {
			$index->model_deleted( $this );
		}
		return parent::delete( );
	}

	public function delete_all( ) {
		foreach ( $this->get_attribute_fields( 'set' ) as $name ) {
			$this->_attributes[ $name ]->model_deleted_all( $this );
		}
		foreach ( $this->_indexs as $index ) {
			$index->model_deleted_all( $this );
		}
		return parent::delete_all( );
	}

	public function save( $force_insert = FALSE ) {
		foreach ( $this->_uniqs as $name => $index ) {
			if ( ! $index->is_uniq( $this ) ) {
				\Notification::push( $name . ' already exists !', \Notification::ERROR );
				return false;
			}
		}
		$this->attributes_version ++;
		$save = parent::save( $force_insert );
		foreach ( $this->get_attribute_fields( ) as $name ) {
			$this->_attributes[ $name ]->model_saved( $this );
		}
		foreach ( $this->_indexs as $index ) {
			$index->model_saved( $this );
		}
		
		return $save;
	}


	/*************************************************************************
	  EXTENDED METHODS
	 *************************************************************************/
	protected function init_by_data( $data ) {
		if ( isset( $data[ 'attributes_version' ] ) ) {
			$this->attributes_version = $data[ 'attributes_version' ];
		}
		$init = parent::init_by_data( $data );
		foreach ( $this->get_attribute_fields( ) as $name ) {
			$this->_attributes[ $name ]->model_initialized( $this );
		}
		return $init;
	}

	protected function init_attributes_by_data( $data ) {
		$attributes = unserialize( $data[ 'attributes' ] );
		if ( $data[ 'type_version' ] < $this->type_version ) {
			$attributes = $this->upgrade( $attributes, $data[ 'type_version' ] );
		} else if ( $data[ 'type_version' ] > $this->type_version ) {
			// TODO: Log a warning, this is not a normal case.
			$this->type_version = $data[ 'type_version' ];
		}
		if ( is_array( $attributes ) ) {
			foreach ( $attributes as $name => $value ) {
				if ( isset( $this->_attributes[ $name ] ) ) {
					$this->_attributes[ $name ]->init( $value );
				}
			}
		}
		return serialize( $attributes );
	}



	/*************************************************************************
	 ROUTINES
	*************************************************************************/
	/*
	 * Upgrades the model_type of the attributes to the current model
	 * @param Array $attributes the array of attributes on a previous version of the model
	 * @param integer $current_type_version the version of the attributes given as parameters
	 * @return Array $attributes the array of attributes updated to the current model
	 */
	protected function upgrade( $attributes, $attributes_type_version ) {
		return $attributes;
	}

	protected function downgrade( $attributes, $attributes_type_version ) {
		return $attributes;
	}
}

<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type;

class Relation_One extends \Data_Type\Relation {


	/*************************************************************************
	  USER GETTER & SETTER             
	 *************************************************************************/
	public function get_id( ) {
		if ( ! empty( $this->relations ) ) {
			return $this->relations[ 0 ]->get( )->id;
		}
	}

	public function get( ) {
		if ( ! $this->initialized ) {
			$relations = $this->definition->all( );
			if ( $relations->is_empty( ) ) {
				$this->relations = [ ];
			} else {
				$this->relations = array_slice( $relations->to_array( ), 0, 1 );
			}
			$this->initialized = TRUE;
		}
		if ( ! empty( $this->relations ) ) {
			return $this->relations[ 0 ]->get( );
		}
	}
}

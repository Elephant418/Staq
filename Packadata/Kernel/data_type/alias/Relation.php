<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Data_Type\Alias;

class Relation extends Relation\__Parent {



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( $definition ) {
		parent::__construct( function( $model ) use ( $definition ) {
			$relateds = new \Object_List;
			$relations = $definition->set_model( $model )->all( );
			foreach ( $relations as $relation ) {
				$relateds[ ] = $relation->get( );
			}
			return $relateds;
		} , 'Relation' );
	}
}

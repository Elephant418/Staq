<?php

namespace Staq\Core\Data\Stack\Controller;

class Model extends Model\__Parent {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $setting = [ ];



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function action( $id ) {
		// TODO: Implement different actions
		$model = $this->new_model( )->by_id( $id );
		return $model;
	}



	/*************************************************************************
	  PRIVATE METHODS           
	 *************************************************************************/
	protected function new_model( ) {
		$class = 'Stack\\' . \Staq\Util::stack_sub_query( $this );
		return new $class;
	}
}
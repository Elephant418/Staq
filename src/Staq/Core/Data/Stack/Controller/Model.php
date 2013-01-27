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
		$model = $this->new_model( )->by_id( $id );
		return $model;
	}

	public function action_delete( $id ) {
		$model = $this->action( $id );
		$model->delete( );
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
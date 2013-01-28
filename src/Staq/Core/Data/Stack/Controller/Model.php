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
	public function action_list( ) {
		$models = $this->new_model( )->all( );
		$page = new \Stack\View;
		$page[ 'content'  ] = $models;
		$page[ 'template' ] = 'model/list/' . $this->get_sub_template( );
		return $page;
	}

	public function action_view( $id ) {
		$model = $this->new_model( )->by_id( $id );
		$page = new \Stack\View;
		$page[ 'content'  ] = $model;
		$page[ 'template' ] = 'model/view/' . $this->get_sub_template( );
		return $page;
	}



	/*************************************************************************
	  PRIVATE METHODS           
	 *************************************************************************/
	protected function new_model( ) {
		$class = 'Stack\\' . \Staq\Util::stack_sub_query( $this );
		return new $class;
	}

	protected function get_sub_template( ) {
		return strtolower( \Staq\Util::stack_sub_query( $this->new_model( ), '/' ) ) . '.html';
	}
}
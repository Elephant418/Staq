<?php

namespace Staq\Core\Data\Stack\Controller;

class Model extends Model\__Parent {



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function action_list( ) {
		$models = $this->new_model( )->all( );
		$page = ( new \Stack\View )->by_name( \Staq\Util::stack_sub_query( $this ), 'Model\\List' );
		$page[ 'content'  ] = $models;
		return $page;
	}

	public function action_view( $id ) {
		$model = $this->new_model( )->by_id( $id );
		if ( $model->exists( ) ) {
			$page = ( new \Stack\View )->by_name( \Staq\Util::stack_sub_query( $this ), 'Model\\View' );
			$page[ 'content'  ] = $model;
			return $page;
		}
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
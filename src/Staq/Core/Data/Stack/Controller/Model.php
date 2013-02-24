<?php

namespace Staq\Core\Data\Stack\Controller;

class Model extends Model\__Parent {



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function action_list( ) {
		$models = $this->new_model( )->all( );
		$page = ( new \Stack\View )->by_name( $this->model_name( ), 'Model_List' );
		$page[ 'content'  ] = $models;
		return $page;
	}

	public function action_view( $id ) {
		$model = $this->new_model( )->by_id( $id );
		if ( $model->exists( ) ) {
			$page = ( new \Stack\View )->by_name( $this->model_name( ), 'Model_View' );
			$page[ 'content'  ] = $model;
			return $page;
		}
	}



	/*************************************************************************
	  PRIVATE METHODS           
	 *************************************************************************/
	protected function model_name( ) {
		return \Staq\Util::getStackSubQuery( $this->model_class( ) );
	}

	protected function model_class( ) {
		return 'Stack\\' . \Staq\Util::getStackSubQuery( $this );
	}

	protected function new_model( ) {
		$class = $this->model_class( );
		return new $class;
	}
}
<?php

namespace Staq\App\BackOffice\Stack\Controller;

class Model {



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function actionList( ) {
		$page = ( new \Stack\View )->byName( $this->modelName( ), 'Model_List' );
		$fields = ( new \Stack\Setting )
			->parse( 'BackOffice' )
			->get( 'list.' . $this->modelName( ) );
		if ( empty( $fields ) ) {
			$fields = [ 'id' ];
		}
		$page[ 'fields' ] = $fields;
		$page[ 'currentModelType' ] = $this->modelName( );
		$page[ 'models' ] = $this->newModel( )->all( );
		return $page;
	}

	public function actionView( $id ) {
		$model = $this->newModel( )->byId( $id );
		if ( $model->exists( ) ) {
			$page = ( new \Stack\View )->byName( $this->modelName( ), 'Model_View' );
			$page[ 'content'  ] = $model;
			return $page;
		}
	}



	/*************************************************************************
	  PRIVATE METHODS           
	 *************************************************************************/
	protected function modelName( ) {
		return \Staq\Util::getStackSubQuery( $this->modelClass( ) );
	}

	protected function modelClass( ) {
		return 'Stack\\' . \Staq\Util::getStackSubQuery( $this );
	}

	protected function newModel( ) {
		$class = $this->modelClass( );
		return new $class;
	}
}
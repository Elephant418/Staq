<?php

namespace Staq\Core\Data\Stack\Controller;

class Model extends Model\__Parent {



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function actionList( ) {
		$models = $this->newModel( )->all( );
		$page = ( new \Stack\View )->byName( $this->modelName( ), 'Model_List' );
		$page[ 'content'  ] = $models;
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
    PUBLIC METHODS
     *************************************************************************/
    public function getRouteAttributes( $model ) {
        $attributes = [];
        $attributes['id'] = $model->id;
        $attributes['name'] = $model->name();
        return $attributes;
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
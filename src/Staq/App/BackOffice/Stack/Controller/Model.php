<?php

namespace Staq\App\BackOffice\Stack\Controller;

use \Stack\Util\UINotification as Notif;

class Model {



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function actionList( $type ) {
		$page = ( new \Stack\View )->byName( $type, 'Model_List' );
		$fields = ( new \Stack\Setting )
			->parse( 'BackOffice' )
			->get( 'list.' . $type );
		if ( empty( $fields ) ) {
			$fields = [ 'id' ];
		}
		$page[ 'fields' ] = $fields;
		$page[ 'currentModelType' ] = $type;
		$page[ 'models' ] = $this->newModel( $type )->all( );
		return $page;
	}

	public function actionView( $type, $id ) {
		$model = $this->newModel( $type )->byId( $id );
		if ( $model->exists( ) ) {
			$page = ( new \Stack\View )->byName( $type, 'Model_View' );
			$page[ 'currentModelType' ] = $type;
			$page[ 'model' ] = $model;
			return $page;
		}
	}

	public function actionCreate( $type ) {
		$model = $this->newModel( $type );
		return $this->genericActionEdit( $type, $model );
	}

	public function actionEdit( $type, $id ) {
		$model = $this->newModel( $type )->byId( $id );
		if ( $model->exists( ) ) {
			return $this->genericActionEdit( $type, $model );
		}
	}

	public function actionDelete( $type, $id ) {
		$model = $this->newModel( $type )->byId( $id );
		if ( $model->exists( ) ) {
			$model->delete( );
			if ( $model->exists( ) ) {
				Notif::error( 'Model not deleted.' );
				$this->redirectView( $type, $model );
			} else {
				Notif::success( 'Model deleted.' );
				$this->redirectList( $type );
			}
		}
	}



	/*************************************************************************
	  REDIRECT METHODS           
	 *************************************************************************/
	protected function genericActionEdit( $type, $model ) {
		if ( isset( $_POST[ 'model' ] ) ) {
			foreach ( $_POST[ 'model' ] as $name => $value ) {
				$model->set( $name, $value );
			}
			if ( $model->save( ) ) {
				Notif::success( 'Model saved.' );
			} else {
				Notif::error( 'Model not saved.' );
			}
			$this->redirectView( $type, $model );
		}
		$page = ( new \Stack\View )->byName( $type, 'Model_Edit' );
		$page[ 'currentModelType' ] = $type;
		$page[ 'model' ] = $model;
		return $page;
	}



	/*************************************************************************
	  REDIRECT METHODS           
	 *************************************************************************/
	protected function redirectView( $type, $model ) {
		$params = [ ];
		$params[ 'type' ] = $type;
		$params[ 'id' ] = $model->id;
		\Staq\Util::httpRedirectUri( \Staq::App()->getUri( $this, 'view', $params ) );
	}

	protected function redirectList( $type ) {
		$params = [ ];
		$params[ 'type' ] = $type;
		\Staq\Util::httpRedirectUri( \Staq::App()->getUri( $this, 'list', $params ) );
	}



	/*************************************************************************
	  PRIVATE METHODS           
	 *************************************************************************/
	protected function modelClass( $type ) {
		return 'Stack\\Model\\' . $type;
	}

	protected function newModel( $type ) {
		$class = $this->modelClass( $type );
		return new $class;
	}
}
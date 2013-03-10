<?php

namespace Staq\App\BackOffice\Stack\Controller;

use \Stack\Util\UINotification as Notif;

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
			$page[ 'currentModelType' ] = $this->modelName( );
			$page[ 'model' ] = $model;
			return $page;
		}
	}

	public function actionEdit( $id ) {
		$model = $this->newModel( )->byId( $id );
		if ( isset( $_POST[ 'model' ] ) ) {
			foreach ( $_POST[ 'model' ] as $name => $value ) {
				$model->set( $name, $value );
			}
			if ( $model->save( ) ) {
				Notif::success( 'Model saved.' );
			} else {
				Notif::error( 'Model not saved.' );
			}
			$this->redirectView( $model );
		}
		if ( $model->exists( ) ) {
			$page = ( new \Stack\View )->byName( $this->modelName( ), 'Model_Edit' );
			$page[ 'currentModelType' ] = $this->modelName( );
			$page[ 'model' ] = $model;
			return $page;
		}
	}

	public function actionDelete( $id ) {
		$model = $this->newModel( )
			->byId( $id )
			->delete( );
		if ( $model->exists( ) ) {
			Notif::error( 'Model not deleted.' );
			$this->redirectView( $model );
		} else {
			Notif::success( 'Model deleted.' );
			$this->redirectList( );
		}
	}



	/*************************************************************************
	  REDIRECT METHODS           
	 *************************************************************************/
	protected function redirectView( $model ) {
		\Staq\Util::httpRedirectUri( \Staq::App()->getUri( $this, 'view', [ 'id' => $model->id ] ) );
	}

	protected function redirectList( ) {
			\Staq\Util::httpRedirectUri( \Staq::App()->getUri( $this, 'list' ) );
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
<?php

namespace Supersoniq\Packadata\Kernel\Controller;

class Versioned_Model extends \Controller\Model {


	/*************************************************************************
	 CONSTRUCTOR
	*************************************************************************/
	public function __construct( ) {
		parent::__construct( );
		$root = '/' . strtolower( $this->type );
		$this->add_handled_route( 'archives', '/archives' );
		$this->add_handled_route( 'archive', $root . '/archive/:id' );
		$this->add_handled_route( 'see'    , $root . '/archive/see/:id/:versions' );
		$this->add_handled_route( 'restore', $root . '/restore/:id/:versions' );
		$this->add_handled_route( 'erase'  , $root . '/erase/:id(/:versions)' );
	}


	/*************************************************************************
	 ACTION METHODS
	*************************************************************************/
	public function archives( $type = NULL ) {
		$archives= new \Model_Archive( );
		$archives = $archives->all( $type );
		$this->view->archives = $archives;
		$this->view->content = $this->view->render( \View\__Base::DELETED_MODELS_TEMPLATE );
		$this->view->title = 'Archives of Deleted Models';
		return $this->render( \View\__Base::LAYOUT_TEMPLATE );
	}
	public function archive( $id ) {
		$model = $this->model( );
		if ( $model->init_by_id( $id ) ) {
			$this->view->title   = 'Versions of ';
		} else {
			$this->view->title   = 'Archives of ';
		}
		$archive = new \Model_Archive( );
		if ( $archives = $archive->get_model_history( $id, $this->type ) ) {
			$this->view->title .= $this->type . ' ' . $id;
			$this->view->archives = $archives;
			$this->view->content = $this->view->render( \View\__Base::LIST_ARCHIVE_TEMPLATE );
		} else {
			$this->view->title   = 'Archives of this ' . $this->type . ' not found';
		}
		return $this->render( \View\__Base::LAYOUT_TEMPLATE );
	}
	public function see( $id, $versions ) {
		$archive = new \Model_Archive( );
		if ( $archive = $archive->get_model_version( $id, $this->type, array( 'attributes' => $versions ) ) ) {
			$this->view->title = $this->type . ' ' . $id . ' version ' . $archive->model_attributes_version;
			$this->view->archive = $archive;
			$this->view->content = $this->view->render( \View\__Base::VIEW_ARCHIVE_TEMPLATE );
		} else {
			$this->view->title = 'Archive not found';
		}
		return $this->render( \View\__Base::LAYOUT_TEMPLATE );
	}
	public function erase ( $id, $versions = NULL ) {
		$archive = new \Model_Archive( );
		if ( isset( $versions ) ) {
			$archives = $archive->get_model_version( $id, $this->type, array( 'attributes' => $versions  ) );
		} else {
			$archives = $archive->get_model_history( $id, $this->type);
		}
		if ( $archives ) {
			if ( is_array( $archives ) ) {
				foreach ( $archives as $archive ) {
					$archive->delete( );
				}
				\Notification::push( 'Archives of this ' . $this->type . ' deleted with success ! ', \Notification::SUCCESS );
				\Supersoniq\Application::redirect_to_action( $this->type, 'view', array( 'id' => $id ) );
			} else {
				$deleted_version = $archives->model_type_version . '.' . $archives->model_attributes_version;
				$archives->delete( );
				\Notification::push( 'Version ' . $deleted_version . ' of this ' . $this->type . ' deleted with success ! ', \Notification::SUCCESS );
				\Supersoniq\Application::redirect_to_action( $this->type, 'archive', array( 'id' => $id ) );
			}
		} else {
			$this->view->title   = 'Archives of this ' . $this->type . ' not found';
			return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
		}
	}
	public function restore ( $id, $versions ) {
		$archive = new \Model_Archive( );
		if ( $archive = $archive->get_model_version( $id, $this->type, array( 'attributes' => $versions ) ) ) {
			$model_restore = 'Model\\' . $this->type;
			$model = new $model_restore;
			$model->init_by_id( $archive->model_id );
			foreach ( $archive->model_attributes as $attribute => $value ) {
				$model->set( $attribute, $value );
				if ( ! $archive->current_version( $archive->model_id, $this->type ) ) {
					//TODO Restore with the same id for the restoration of a deleted model (following line doesn't work on save)
					//$model->id = $archive->model_id;
				}
			}
			//TODO Decide if we keep the archives or not and what to do with the versions of the restored model
			$restored_version = $archive->model_type_version . '.' . $archive->model_attributes_version;
			$model->save( );
			\Notification::push( $this->type . ' version ' . $restored_version . ' restored with success ! ', \Notification::SUCCESS );
			\Supersoniq\Application::redirect_to_action( $this->type, 'view', array( 'id' => $model->id ) );
		} else {
			$this->view->title   = 'Archive not found';
			return $this->render( \View\__Base::LAYOUT_TEMPLATE );
		}
	}
}

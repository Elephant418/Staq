<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Controller;

abstract class Model extends \Controller\__Base {


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
	public function __construct( ) {
		parent::__construct( );
		$root = '/' . strtolower( $this->type );
		$this->add_handled_route( 'all'   , $root);
		$this->add_handled_route( 'view'  , $root . '/view/:id' );
		$this->add_handled_route( 'create', $root . '/create' );
		$this->add_handled_route( 'edit'  , $root . '/edit/:id' );
		$this->add_handled_route( 'delete', $root . '/delete/:id' );
		$this->add_handled_route( 'archive', $root . '/archive/:id' );
	}


	/*************************************************************************
	  ACTION METHODS                   
	 *************************************************************************/
	public function all( ) {
		$model = $this->model( );
		$this->view->all     = $model->all( );
		$this->view->title   = 'List of ' . $this->type;
		$this->view->content = $this->render_list_model( $model );
		return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
	}
	public function view( $id ) {
		$model = $this->model( );
		if ( $model->init_by_id( $id ) ) {
			$this->view->title   = $this->type . ' ' . $id;
			$this->view->content = $this->render_view_model( $model );
		} else {
			$this->view->title   = $this->type . ' not found';
		}
		return $this->render( \View\__Base::LAYOUT_TEMPLATE );
	}
	public function create( ) {
		$model = $this->model( );
		if ( isset( $_POST[ 'model' ] ) ) {
			foreach ( $_POST[ 'model' ] as $name => $value ) {
				$model->$name = $value;
			}
			$model->save( );
			if ( $model->exists( ) ) {
				\Notification::push( $this->type . ' created with success ! ', \Notification::SUCCESS );
				\Supersoniq\Application::redirect_to_action( $this->type, 'view', array( 'id' => $model->id ) );
			}
			\Notification::push( $this->type . ' not created !', \Notification::ERROR );
		}
		$this->view->title   = 'New ' . $this->type;
		$this->view->content = $this->render_edit_model( $model ); 
		return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
	}
	public function edit( $id ) {
		$model = $this->model( );
		if ( $model->init_by_id( $id ) ) {
			if ( isset( $_POST[ 'model' ] ) ) {
				foreach ( $_POST[ 'model' ] as $name => $value ) {
					$model->$name = $value;
				}
				if ( $model->save( ) ) {
					\Notification::push( $this->type . ' updated with success ! ', \Notification::SUCCESS );
					\Supersoniq\Application::redirect_to_action( $this->type, 'view', array( 'id' => $model->id ) );
				}
				\Notification::push( $this->type . ' not updated !', \Notification::ERROR );
			}
			$this->view->title   = 'New ' . $this->type;
			$this->view->content = $this->render_edit_model( $model );
		} else {
			$this->view->title   = $this->type . ' not found';
		}
		return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
	}
	public function delete( $id ) {
		$model = $this->model( );
		if ( $model->init_by_id( $id ) ) {
			$model->delete( );
			\Notification::push( $this->type . ' deleted with success ! ', \Notification::SUCCESS );
			\Supersoniq\Application::redirect_to_action( $this->type, 'all' );
		} else {
			$this->view->title   = $this->type . ' not found';
			return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
		}
	}
	public function archive( $id ) {
		$model = $this->model( );
		if ( $model->init_by_id( $id ) ) {
			$this->view->current = TRUE;
			$this->view->title   = 'Versions of ';
		} else {
			$this->view->current = FALSE;
			$this->view->title   = 'Archives of ';
		}
		$archive = new \Model_Archive( );
		if ( $archives = $archive->get_object_history( $id, array( 'type' => $this->type ) ) ) {
			$this->view->title .= $this->type . ' ' . $id;
			$this->view->archives = $archives;
			$this->view->content = $this->render_archive_model( $model );
		} else {
			$this->view->title   = $this->type . ' not found';
		}
		return $this->render( \View\__Base::LAYOUT_TEMPLATE );
	}


	/*************************************************************************
	  PROTECTED METHODS                   
	 *************************************************************************/
	public function action_method( $method = NULL ) {
		if ( ! is_a( $this->model( ), 'Model\Defined' ) ) {
			throw new \Exception\Redirect( '/error/view/404', 'Unknown controller "' . $this->type . '"' );
		}
		return parent::action_method( $method );
	}
	protected function model_class( ) {
		return '\\Model\\' . $this->type;
	}
	protected function model( ) {
		$class = $this->model_class( );
		return new $class;
	}
	protected function model_all_url( ) {
		return '/' . strtolower( $this->type ) . '/';
	}
	protected function model_create_url( ) {
		return '/' . strtolower( $this->type ) . '/create/';
	}
	protected function model_view_url( $model ) {
		return $this->model_action_url( $model, 'view' );
	}
	protected function model_edit_url( $model ) {
		return $this->model_action_url( $model, 'edit' );
	}
	protected function model_delete_url( $model ) {
		return $this->model_action_url( $model, 'delete' );
	}
	protected function model_archive_url( $model ) {
		return $this->model_action_url( $model, 'archive' );
	}
	protected function model_action_url( $model, $action ) {
		return '/' . strtolower( $this->type ) . '/' . $action . '/' . $model->id;
	}
	protected function init_var( $model = NULL ) {
		$action_url             = array( );
		$action_url[ 'all' ]    = $this->model_all_url( );
		$action_url[ 'create' ] = $this->model_create_url( );
		if ( $model ) {
			$this->view->model = $model;
			$action_url[ 'view' ]   = $this->model_view_url( $model );
			$action_url[ 'edit' ]   = $this->model_edit_url( $model );
			$action_url[ 'delete' ] = $this->model_delete_url( $model );
			$action_url[ 'archive' ] = $this->model_archive_url( $model );
		}
		$this->view->action_url = $action_url;
	}


	/*************************************************************************
	  PROTECTED RENDER METHODS                   
	 *************************************************************************/
	protected function render_view_model( $model ) {
		$this->init_var( $model );
		return $this->view->render( \View\__Base::VIEW_MODEL_TEMPLATE );
	}
	protected function render_edit_model( $model ) {
		$this->init_var( $model );
		return $this->view->render( \View\__Base::EDIT_MODEL_TEMPLATE );
	}
	protected function render_list_model( ) {
		$this->init_var( );
		return $this->view->render( \View\__Base::LIST_MODEL_TEMPLATE );
	}
	protected function render_archive_model( $model ) {
		$this->init_var( $model );
		return $this->view->render( \View\__Base::ARCHIVE_MODEL_TEMPLATE );
	}
}


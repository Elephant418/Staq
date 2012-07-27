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
	}


	/*************************************************************************
	  ACTION METHODS                   
	 *************************************************************************/
	public function all( ) {
		$model = $this->model( );
		$this->view->all     = $model->all( );
		$this->view->title   = 'List of ' . $this->type;
		$this->view->content = $this->view->render( \View\__Base::LIST_MODEL_TEMPLATE );
		return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
	}
	public function view( $id ) {
		$model = $this->model( );
		$this->view->model = $model;
		if ( $model->init_by_id( $id ) ) {
			$this->view->title   = $this->type . ' ' . $id;
			$this->view->content = $this->view->render( \View\__Base::VIEW_MODEL_TEMPLATE );
		} else {
			$this->view->title   = $this->type . ' not found';
		}
		return $this->render( \View\__Base::LAYOUT_TEMPLATE );
	}
	public function create( ) {
		$model = $this->model( );
		$this->view->model = $model;
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
		$this->view->content = $this->view->render( \View\__Base::EDIT_MODEL_TEMPLATE );
		return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
	}
	public function edit( $id ) {
		$model = $this->model( );
		$this->view->model = $model;
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
			$this->view->title   = 'Edit ' . $this->type;
			$this->view->content = $this->view->render( \View\__Base::EDIT_MODEL_TEMPLATE );
		} else {
			$this->view->title   = $this->type . ' not found';
		}
		return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
	}
	public function delete( $id ) {
		$model = $this->model( );
		$this->view->model = $model;
		if ( $model->init_by_id( $id ) ) {
			$model->delete( );
			\Notification::push( $this->type . ' deleted with success ! ', \Notification::SUCCESS );
			\Supersoniq\Application::redirect_to_action( $this->type, 'all' );
		} else {
			$this->view->title   = $this->type . ' not found';
			return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
		}
	}


	/*************************************************************************
	  PROTECTED METHODS                   
	 *************************************************************************/
	public function action_method( $method = NULL ) {
		return parent::action_method( $method );
	}
	protected function model_class( ) {
		return '\\Model\\' . $this->type;
	}
	protected function model( ) {
		$class = $this->model_class( );
		return new $class;
	}
}

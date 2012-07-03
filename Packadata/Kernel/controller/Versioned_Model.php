<?php

namespace Supersoniq\Packadata\Kernel\Controller;

class Versioned_Model extends \Controller\Model {


	/*************************************************************************
	 CONSTRUCTOR
	*************************************************************************/
	public function __construct( ) {
		parent::__construct( );
		$root = '/' . strtolower( $this->type );
		$this->add_handled_route( 'archive', $root . '/archive/:id' );
		$this->add_handled_route( 'archives', '/archives' );
	}


	/*************************************************************************
	 ACTION METHODS
	*************************************************************************/
	public function archives( ) {
		$models= new \Model_Archive( );
		$content = '';
		$models = $models->all( );
		$ignore = array( );
		//TODO Better method using the template for archives
		// 			$this->view->archives = $archives;
		//			$this->view->content = $this->render_archive_model( $model );
		foreach ( $models as $model ) {
			if ( ! in_array( $model->model_id, $ignore ) ) {
				$iterator = new \Model_Archive( );
				$objects = $iterator->get_object_history( $model->model_id );
				if ( $objects ) {
					$content .= '<h3>'. $model->model_type . ' number ' . $model->model_id . ' : </h3>';
					foreach ( $objects as $object ) {
						$attributes = $object->model_attributes;
						if ( $object == $model->last_version( $model->model_id ) ) {
							$content .= '<h4>Last model</h4>';
							if ( ! $model->current_version( $model->model_id ) ) {
								$content .= 'Warning: has been deleted (no current version)<br/>';
							}
						} else {
							if ( ! \String::ends_with($content, '</h3>') ) {
								$content .= '<br/>';
							}
						}
						$content .= 'Modification date : ' . date_format( \DateTime::createFromFormat( 'Y-m-d G:i:s', $object->date_version ), 'd/m/Y' ) . '<br/>';
						$content .= 'Modification hour : ' . date_format( \DateTime::createFromFormat( 'Y-m-d G:i:s', $object->date_version ), 'G:i' ) . '<br/>';
						$content .= 'Changed by the IP : ' . $object->ip_version . '<br/>';
						$content .= 'Version of the model : ' . $object->model_type_version . '<br/>';
						$content .= 'Version of the attributes : ' . $object->model_attributes_version . '<br/>';
						$content .= 'Values of the attributes : <br/>';
						foreach ( $attributes as $key => $value ) {
							$content .= $key . ' => ' . $value . ' // ';
						}
						$content .= '<br/>';
					}
				}
				$ignore[] = $model->model_id; 
			}
		}
		$this->view->title = 'List of Archives';
		$this->view->content = $content;
		return $this->render( \View\__Base::LAYOUT_TEMPLATE );
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
	public function erase ( $id ) {
		$archive = new \Model_Archive( );
		if ( $archives = $archive->get_object_history( $id, array( 'type' => $this->type ) ) ) {
			foreach ( $archives as $archive ) {
				$archive->delete( );
			}
			\Notification::push( 'Archives of ' . $this->type . ' deleted with success ! ', \Notification::SUCCESS );
			\Supersoniq\Application::redirect_to_action( $this->type, 'all' );
		} else {
			$this->view->title   = 'Archives of ' . $this->type . ' not found';
			return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
		}
	}
	public function restore ( $id ) {
		$archive = new \Model_Archive( );
	}


	/*************************************************************************
	 PROTECTED METHODS
	*************************************************************************/
	protected function model_archive_url( $model ) {
		return $this->model_action_url( $model, 'archive' );
	}
	protected function model_erase_url( $model ) {
		return $this->model_action_url( $model, 'erase' );
	}
	protected function model_restore_url( $model ) {
		return $this->model_action_url( $model, 'restore' );
	}
	protected function init_var( $model = NULL ) {
		$action_url = parent::init_var( $model );
		if ( $model ) {
			$action_url[ 'archive' ]	= $this->model_archive_url( $model );
			$action_url[ 'erase' ]		= $this->model_erase_url( $model );
			$action_url[ 'restore' ]	= $this->model_restore_url( $model );
		}
		$this->view->action_url = $action_url;
	}

	/*************************************************************************
	 PROTECTED RENDER METHODS
	*************************************************************************/
	protected function render_archive_model( $model ) {
		$this->init_var( $model );
		return $this->view->render( \View\__Base::ARCHIVE_MODEL_TEMPLATE );
	}
}

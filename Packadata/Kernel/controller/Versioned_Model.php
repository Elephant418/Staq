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
		$this->add_handled_route( 'see', $root . '/archive/see/:id/:versions' );
		$this->add_handled_route( 'restore', $root . '/restore/:id/:versions' );
		$this->add_handled_route( 'erase'  , $root . '/erase/:id(/:versions)' );
	}


	/*************************************************************************
	 ACTION METHODS
	*************************************************************************/
	public function archives( $type = NULL ) {
		
		//TODO Decide whether we place here all the archives or the archives of models who were deleted
		//Second choice seems more logical as seeing everything isn't a greaaat functionality...
		$models= new \Model_Archive( );
		$models = $models->all( $type );
		
		///Refactor
		$content = '';
		$ignore = array( );
		//TODO Better method using the template for archives
		// 			$this->view->archives = $archives;
		//			$this->view->content = $this->render_archive_model( $model );
		foreach ( $models as $model ) {
			if ( ! in_array( $model->model_id, $ignore ) ) {
				$iterator = new \Model_Archive( );
				$objects = $iterator->get_object_history( $model->model_id, $model->model_type );
				if ( $objects ) {
					$content .= '<h3>'. $model->model_type . ' number ' . $model->model_id . ' : </h3>';
					foreach ( $objects as $object ) {
						$attributes = $object->model_attributes;
						if ( $object == $model->last_version( $model->model_id, $model->model_type ) ) {
							$content .= '<h4>Last model</h4>';
							if ( ! $model->current_version( $model->model_id, $model->model_type ) ) {
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
			$this->view->title   = 'Versions of ';
		} else {
			$this->view->title   = 'Archives of ';
		}
		$archive = new \Model_Archive( );
		if ( $archives = $archive->get_object_history( $id, $this->type ) ) {
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
		if ( $archive = $archive->get_object_version( $id, $this->type, array( 'attributes' => $versions ) ) ) {
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
			$archives = $archive->get_object_version( $id, $this->type, array( 'attributes' => $versions  ) );
		} else {
			$archives = $archive->get_object_history( $id, $this->type);
		}
		if ( $archives ) {
			if ( is_array( $archives ) ) {
				foreach ( $archives as $archive ) {
					$archive->delete( );
				}
				\Notification::push( 'Archives of this ' . $this->type . ' deleted with success ! ', \Notification::SUCCESS );
				\Supersoniq\Application::redirect_to_action( $this->type, 'view', array( 'id' => $id ) );
			} else {
				$deleted_version = $archives->model_type_version . '.' .$archives->model_attributes_version;
				$archives->delete( );
				\Notification::push( 'Version ' . $deleted_version . ' of this ' . $this->type . ' deleted with success ! ', \Notification::SUCCESS );
				\Supersoniq\Application::redirect_to_action( $this->type, 'archive', array( 'id' => $id ) );
			}
		} else {
			$this->view->title   = 'Archives of this ' . $this->type . ' not found';
			return $this->render( \View\__Base::LAYOUT_TEMPLATE ); 
		}
	}
	public function restore ( $id, $versions = NULL ) {
		$archive = new \Model_Archive( );
		if ( $archive = $archive->get_object_version( $id, $this->type, array( 'attributes' => $versions ) ) ) {
			$model_restore = 'Model\\' . $this->type;
			$model = new $model_restore;
			//TODO
		} else {
			$this->view->title   = 'Archive not found';
			return $this->render( \View\__Base::LAYOUT_TEMPLATE );
		}
	}
}

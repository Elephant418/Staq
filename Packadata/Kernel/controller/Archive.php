<?php

namespace Supersoniq\Packadata\Kernel\Controller;

class Archive extends \Controller\__Base {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $handled_routes = array( 
		'view' => '/archive'
	);


	/*************************************************************************
	 ACTION METHODS
	*************************************************************************/
	public function view( ) {
		$models= new \Model_Archive( );
		$content = '';
		$models = $models->all( );
		$ignore = array( );
		
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
}

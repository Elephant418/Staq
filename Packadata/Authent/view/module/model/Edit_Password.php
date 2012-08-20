<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Authent\View\Module\Model;

abstract class Edit_Password extends Edit_Password\__Parent {


	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
	public function fill( $template, $parameters = [ ] ) {
		$controller = $this->get_controller( );
		$model = $controller->get( $parameters[ 'id' ] );
		if ( isset( $_POST[ 'model' ][ 'password' ] ) ) {
			if ( $controller->edit_password( $model, $_POST[ 'model' ][ 'password' ] ) ) {
				\Supersoniq\redirect_to_page( 'view', array( 'id' => $model->id ) );
			}
		}
		$template->model = $model;
		return $template;
	}

}

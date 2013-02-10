<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\View\Stack\Controller;

class Error extends Error\__Parent {



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function action_view( $code ) {
		parent::action_view( $code );
		$page = new \Stack\View\Error;
		$page[ 'code' ] = $code;
		$page[ 'message' ] = '';
		return $page;
	}

}

?>
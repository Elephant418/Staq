<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Test\Staq\Project\Router\Stack\Controller;

class Error extends Error\__Parent {



	/*************************************************************************
	  ACTION METHODS           
	 *************************************************************************/
	public function action_view( $code ) {
		return 'error ' . $code;
	}

}

?>
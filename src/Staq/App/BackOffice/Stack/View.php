<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\App\BackOffice\Stack;

class View extends View\__Parent {



	/*************************************************************************
	  PRIVATE METHODS              
	 *************************************************************************/
	protected function addVariables( ) {
		parent::addVariables( );
		$modelTypes = ( new \Stack\Setting )
			->parse( 'BackOffice' )
			->get( 'model' );
		$this[ 'modelTypes' ] = $modelTypes;
	}
}

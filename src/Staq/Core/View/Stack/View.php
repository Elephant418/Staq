<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\View\Stack;

class View extends \Pixel418\Iniliq\ArrayObject {



	/*************************************************************************
	  ATTRIBUTES              
	 *************************************************************************/
	public $var;
	protected $twig;



	/*************************************************************************
	  CONSTRUCTOR METHODS              
	 *************************************************************************/
	public function __construct( ) {
		\Twig_Autoloader::register( );
		$loader = new \Twig_Loader_Filesystem( \Staq\Application::get_extensions( 'template' ) );
		$params = [ ];
		if ( FALSE && $cache_path = \Staq\Application::get_path( 'cache/twig', TRUE ) ) {
			$params[ 'cache' ] = $cache_path;
		}
		$this->twig = new \Twig_Environment( $loader, $params );
		$this[ 'template' ] = 'index.html';
	}



	/*************************************************************************
	  PUBLIC METHODS              
	 *************************************************************************/
	public function render( ) {
		return $this->twig->render( $this[ 'template' ], $this->getArrayCopy( ) );
	}
}

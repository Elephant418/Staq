<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\View\Stack;

class View extends \Pixel418\Iniliq\ArrayObject {



	/*************************************************************************
	  ATTRIBUTES              
	 *************************************************************************/
	public $var;
	public static $setting = [
		'twig.cache' => 'off'
	];
	protected $twig;



	/*************************************************************************
	  CONSTRUCTOR METHODS              
	 *************************************************************************/
	public function __construct( ) {
		\Twig_Autoloader::register( );
		$loader = $this->get_twig_environment_loader( );
		$params = $this->get_twig_environment_parameters( );
		$this->twig = new \Twig_Environment( $loader, $params );
		$this->extend_twig( );
		$this->init_default_variables( );
	}



	/*************************************************************************
	  PUBLIC METHODS              
	 *************************************************************************/
	public function render( ) {
		$template = $this->loadTemplate( );
		return $template->render( $this->getArrayCopy( ) );
	}
	public function loadTemplate( ) {
		$template = $this[ 'template' ];
		while ( TRUE ) {
			if ( \Staq\App::get_file_path( 'template/' . $template ) ) {
				break;
			} 
			if ( \UString::has( $template, '/' ) ) {
				$template = \UString::substr_before_last( $template, '/' ) . '.html';
			} else {
				$template = 'index.html';
				break;
			}
		}
		return $this->twig->loadTemplate( $template );
	}



	/*************************************************************************
	  PRIVATE METHODS              
	 *************************************************************************/
	protected function get_twig_environment_loader( ) {
		return new \Twig_Loader_Filesystem( \Staq\App::get_extensions( 'template' ) );
	}
	protected function get_twig_environment_parameters( ) {
		$params = [ ];
		$settings = ( new \Stack\Setting )->parse( $this );
		if ( $settings->get_as_boolean( 'twig.cache' ) ) {
			if ( $cache_path = \Staq\App::get_path( 'cache/twig', TRUE ) ) {
				$params[ 'cache' ] = $cache_path;
			}
		}
		return $params;
	}
	protected function extend_twig( ) {
		$link = new \Twig_SimpleFilter( 'link', function( $path ) {
			return \Staq\App::get_base_uri( ) . '/' . $path;
		});
		$this->twig->addFilter( $link );
	}
	protected function init_default_variables( ) {
		$this[ 'template' ] = 'index.html';
	}
}

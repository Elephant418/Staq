<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\View\Stack;

class View extends \Pixel418\Iniliq\ArrayObject {



	/*************************************************************************
	  ATTRIBUTES              
	 *************************************************************************/
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
	public function by_name( $name, $prefix = NULL ) {
		$class = [ 'Stack\\View' ];
		\UString::do_not_start_with( $prefix, [ '\\', '_' ] );
		\UString::do_not_end_with( $prefix, [ '\\', '_' ] );
		if ( ! empty( $prefix ) ) {
			$class[ ] = $prefix;
		}
		\UString::do_not_start_with( $name, [ '\\', '_' ] );
		\UString::do_not_end_with( $name, [ '\\', '_' ] );
		if ( ! empty( $name ) ) {
			$class[ ] = $name;
		}
		$class = implode( '\\', $class );
		return new $class;
	}



	/*************************************************************************
	  PUBLIC METHODS              
	 *************************************************************************/
	public function render( ) {
		if ( ! empty( $_GET ) ) {
			$this->entry_get( );
		}
		if ( ! empty( $_POST ) ) {
			$this->entry_post( );
		}
		$this->add_variables( );
		$template = $this->loadTemplate( );
		return $template->render( $this->getArrayCopy( ) );
	}
	public function loadTemplate( ) {
		$template = strtolower( \Staq\Util::stack_sub_query( $this, '/' ) ) . '.html';
		$template = str_replace( '_', '/', $template );
		while ( TRUE ) {
			if ( \Staq::App()->get_file_path( 'template/' . $template ) ) {
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
	  OVERRIDABLE METHODS              
	 *************************************************************************/
	public function entry_get( ) {
	}
	public function entry_post( ) {
	}
	public function add_variables( ) {
	}




	/*************************************************************************
	  PRIVATE METHODS              
	 *************************************************************************/
	protected function get_twig_environment_loader( ) {
		return new \Twig_Loader_Filesystem( \Staq::App()->get_extensions( 'template' ) );
	}
	protected function get_twig_environment_parameters( ) {
		$params = [ ];
		$settings = ( new \Stack\Setting )->parse( 'Application.ini' );
		if ( $settings->get_as_boolean( 'cache.twig' ) ) {
			if ( $cache_path = \Staq::App()->get_path( 'cache/twig/', TRUE ) ) {
				$params[ 'cache' ] = $cache_path;
			}
		}
		return $params;
	}
	protected function extend_twig( ) {
		$public = function( $path ) {
			\UString::do_start_with( $path, '/' );
			return \Staq::App()->get_base_uri( ) . $path;
		};
		$route = function( $controller, $action ) use ( $public ) {
			$parameters = array_slice( func_get_args( ), 2 );
			$uri = \Staq::App()->get_uri( $controller, $action, $parameters );
			return $public( $uri );
		};
		$route_model_action = function( $action, $model ) use ( $route ) {
			return $route( \Staq\Util::stack_query( $model ), $action, $model->id );
		};
		$route_model = function( $model ) use ( $route_model_action ) {
			return $route_model_action( 'view', $model );
		};
		$public_filter = new \Twig_SimpleFilter( 'public', $public );
		$this->twig->addFilter( $public_filter );
		$public_function = new \Twig_SimpleFunction( 'public', $public );
		$this->twig->addFunction( $public_function );
		$route_function = new \Twig_SimpleFunction( 'route', $route );
		$this->twig->addFunction( $route_function );
		$route_function = new \Twig_SimpleFunction( 'route_model_*', $route_model_action );
		$this->twig->addFunction( $route_function );
		$route_function = new \Twig_SimpleFunction( 'route_model', $route_model );
		$this->twig->addFunction( $route_function );
	}
	protected function init_default_variables( ) {
	}
}

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
		$loader = $this->getTwigEnvironmentLoader( );
		$params = $this->getTwigEnvironmentParameters( );
		$this->twig = new \Twig_Environment( $loader, $params );
		$this->extendTwig( );
		$this->initDefaultVariables( );
	}
	public function byName( $name, $prefix = NULL ) {
		$class = [ 'Stack\\View' ];
		\UString::doNotStartWith( $prefix, [ '\\', '_' ] );
		\UString::doNotEndWith( $prefix, [ '\\', '_' ] );
		if ( ! empty( $prefix ) ) {
			$class[ ] = $prefix;
		}
		\UString::doNotStartWith( $name, [ '\\', '_' ] );
		\UString::doNotEndWith( $name, [ '\\', '_' ] );
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
			$this->entryGet( );
		}
		if ( ! empty( $_POST ) ) {
			$this->entryPost( );
		}
		$this->addVariables( );
		$template = $this->loadTemplate( );
		return $template->render( $this->getArrayCopy( ) );
	}
	public function loadTemplate( ) {
		$template = strtolower( \Staq\Util::getStackSubQuery( $this, '/' ) ) . '.html';
		$template = str_replace( '_', '/', $template );
		while ( TRUE ) {
			if ( \Staq::App()->getFilePath( 'template/view/' . $template ) ) {
				break;
			} 
			if ( \UString::has( $template, '/' ) ) {
				$template = \UString::substrBeforeLast( $template, '/' ) . '.html';
			} else {
				$template = 'index.html';
				break;
			}
		}
		return $this->twig->loadTemplate( 'view/' . $template );
	}



	/*************************************************************************
	  OVERRIDABLE METHODS              
	 *************************************************************************/
	protected function entryGet( ) {
	}
	protected function entryPost( ) {
	}
	protected function addVariables( ) {
		$this[ 'UINotification' ] = \Stack\Util\UINotification::pull( );
	}




	/*************************************************************************
	  PRIVATE METHODS              
	 *************************************************************************/
	protected function getTwigEnvironmentLoader( ) {
		return new \Twig_Loader_Filesystem( \Staq::App()->getExtensions( 'template' ) );
	}
	protected function getTwigEnvironmentParameters( ) {
		$params = [ ];
		$settings = ( new \Stack\Setting )->parse( 'Application.ini' );
		if ( $settings->getAsBoolean( 'cache.twig' ) ) {
			if ( $cachePath = \Staq::App()->getPath( 'cache/twig/', TRUE ) ) {
				$params[ 'cache' ] = $cachePath;
			}
		}
		return $params;
	}
	protected function extendTwig( ) {
		$public = function( $path ) {
			\UString::doStartWith( $path, '/' );
			return \Staq::App()->getBaseUri( ) . $path;
		};
		$route = function( $controller, $action ) use ( $public ) {
			$parameters = array_slice( func_get_args( ), 2 );
			$uri = \Staq::App()->getUri( $controller, $action, $parameters );
			return $public( $uri );
		};
		$routeModelAction = function( $action, $model ) use ( $route ) {
			return $route( \Staq\Util::getStackQuery( $model ), $action, $model->id );
		};
		$routeModel = function( $model ) use ( $routeModelAction ) {
			return $routeModelAction( 'view', $model );
		};
		$publicFilter = new \Twig_SimpleFilter( 'public', $public );
		$this->twig->addFilter( $publicFilter );
		$publicFunction = new \Twig_SimpleFunction( 'public', $public );
		$this->twig->addFunction( $publicFunction );
		$routeFunction = new \Twig_SimpleFunction( 'route', $route );
		$this->twig->addFunction( $routeFunction );
		$routeFunction = new \Twig_SimpleFunction( 'route_model_*', $routeModelAction );
		$this->twig->addFunction( $routeFunction );
		$routeFunction = new \Twig_SimpleFunction( 'route_model', $routeModel );
		$this->twig->addFunction( $routeFunction );
	}
	protected function initDefaultVariables( ) {
	}
}

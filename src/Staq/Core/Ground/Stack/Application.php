<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Ground\Stack;

class Application {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $extensions;
	protected $name;
	protected $root_uri;
	protected $platform;
	protected $router;
	protected $controllers = [ ];



	/*************************************************************************
	  GETTER             
	 *************************************************************************/
	public function get_extensions( $filter = NULL ) {
		$extensions = $this->extensions;
		if ( $filter ) {
			foreach ( $extensions as $key => $extension ) {
				if ( isset( $extension[ $filter ] ) ) {
					$extensions[ $key ] = $extension[ $filter ];
				} else {
					$extensions[ $key ] = NULL;
				}
			}
		}
		return $extensions;
	}

	public function get_name( ) {
		return $this->name;
	}

	public function get_root_uri( ) {
		return $this->root_uri;
	}

	public function get_platform( ) {
		return $this->platform;
	}

	public function get_current_uri( ) {
		return $this->router->get_current_uri( );
	}

	public function get_last_exception( ) {
		return $this->router->get_last_exception( );
	}
	


	/*************************************************************************
	  SETTER             
	 *************************************************************************/
	public function add_controller( $uri, $controller ) {
		$this->controllers[ ] = func_get_args( );
		return $this;
	}

	public function set_platform( $platform ) {
		$this->platform = $platform;
		return $this;
	}



	/*************************************************************************
	  INITIALIZATION             
	 *************************************************************************/
	public function __construct( $extensions, $name, $root_uri, $platform ) {
		$this->extensions = $extensions;
		$this->name       = $name;
		$this->root_uri   = $root_uri;
		$this->platform   = $platform;
	}



	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function run( ) {
		$this->router = new \Stack\Router( $this->controllers );
		$uri          = \UString::substr_before( $_SERVER[ 'REQUEST_URI' ], '?' );
		echo $this->router->resolve( $uri );
	}
}

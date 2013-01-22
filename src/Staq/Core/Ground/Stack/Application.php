<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Ground\Stack;

class Application {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $extensions;
	protected $root_uri;
	protected $platform;
	protected $router;
	protected $controllers = [ ];



	/*************************************************************************
	  GETTER             
	 *************************************************************************/
	public function get_extensions( $file = NULL ) {
		$extensions = $this->extensions;
		if ( ! empty( $file ) ) {
			\UString::do_start_with( $file, DIRECTORY_SEPARATOR );
			array_walk( $extensions, function( &$a ) use ( $file ) {
				$a = realpath( $a . $file );
			} );
			$extensions = array_filter( $extensions, function( $a ) {
				return ( $a !== FALSE );
			} );
		}
		return $extensions;
	}

	public function get_extension_namespaces( ) {
		return array_keys( $this->extensions );
	}

	public function get_namespace( ) {
		return reset( $this->get_extension_namespaces( ) );
	}

	public function get_path( $file = NULL, $create = FALSE ) {
		$path = reset( $this->extensions );
		if ( ! empty( $file ) ) {
			\UString::do_start_with( $file, DIRECTORY_SEPARATOR );
			$path .= $file;
			$real_path = realpath( $path );
			if ( $real_path == FALSE && $create ) {
				if ( mkdir( $path, 0755, TRUE ) ) {
					$real_path = realpath( $path );
				}
			}
			$path = $real_path;
		}
		return $path;
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
	public function __construct( $extensions, $root_uri, $platform ) {
		$this->extensions = $extensions;
		$this->root_uri   = $root_uri;
		$this->platform   = $platform;
	}

	public function initialize( ) {
		$settings = ( new \Stack\Setting )->parse( $this );

		// Display errors
		$display_errors = 0;
		if ( $settings->get_as_boolean( 'error.display_errors' ) ) {
			$display_errors = 1;
		}
		ini_set( 'display_errors', $display_errors );

		// Level reporting
		$level = $settings->get_as_constant( 'error.error_reporting', 0 );
		error_reporting( $level );

		return $this;
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

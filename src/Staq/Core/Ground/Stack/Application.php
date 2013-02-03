<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Ground\Stack;

class Application {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $extensions;
	protected $base_uri;
	protected $platform;



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

	public function get_file_path( $file = NULL ) {
		$paths = $this->get_extensions( $file );
		if ( ! empty( $paths ) ) {
			return reset( $paths );
		}
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

	public function get_base_uri( ) {
		return $this->base_uri;
	}

	public function get_platform( ) {
		return $this->platform;
	}
	


	/*************************************************************************
	  SETTER             
	 *************************************************************************/
	public function set_platform( $platform ) {
		$this->platform = $platform;
		$this->initialize( );
		return $this;
	}

	public function set_base_uri( $base_uri ) {
		\UString::do_not_end_with( $base_uri, '/' );
		\UString::do_start_with( $base_uri, '/' );
		$this->base_uri = $base_uri;
		return $this;
	}



	/*************************************************************************
	  INITIALIZATION             
	 *************************************************************************/
	public function __construct( $extensions, $base_uri, $platform ) {
		$this->extensions = $extensions;
		$this->set_base_uri( $base_uri );
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
		$level = $settings->get( 'error.error_reporting' );
		if ( ! is_numeric ( $level ) ) {
			$level = 0;
		}
		error_reporting( $level );

		return $this;
	}
}

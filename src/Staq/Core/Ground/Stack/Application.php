<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Ground\Stack;

class Application {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $extensions;
	protected $baseUri;
	protected $platform;
	protected $initialized = FALSE;



	/*************************************************************************
	  GETTER             
	 *************************************************************************/
	public function getExtensions( $file = NULL ) {
		$extensions = $this->extensions;
		if ( ! empty( $file ) ) {
			\UString::doStartWith( $file, DIRECTORY_SEPARATOR );
			array_walk( $extensions, function( &$a ) use ( $file ) {
				$a = realpath( $a . $file );
			} );
			$extensions = array_filter( $extensions, function( $a ) {
				return ( $a !== FALSE );
			} );
		}
		return $extensions;
	}

	public function getFilePath( $file = NULL ) {
		$paths = $this->getExtensions( $file );
		if ( ! empty( $paths ) ) {
			return reset( $paths );
		}
	}

	public function getExtensionNamespaces( ) {
		return array_keys( $this->extensions );
	}

	public function getNamespace( ) {
		return reset( $this->getExtensionNamespaces( ) );
	}

	public function getPath( $file = NULL, $create = FALSE ) {
		$path = reset( $this->extensions );
		if ( ! empty( $file ) ) {
			\UString::doStartWith( $file, DIRECTORY_SEPARATOR );
			$path .= $file;
			$real_path = realpath( $path );
			if ( $real_path == FALSE && $create ) {
				if ( @mkdir( $path, 0755, TRUE ) ) {
					$real_path = realpath( $path );
				}
			}
			$path = $real_path;
		}
		return $path;
	}

	public function getBaseUri( ) {
		return $this->baseUri;
	}

	public function getPlatform( ) {
		return $this->platform;
	}

	public function isInitialized( ) {
		return $this->initialized;
	}
	


	/*************************************************************************
	  SETTER             
	 *************************************************************************/
	public function setPlatform( $platform ) {
		$this->platform = $platform;
		$this->initialize( );
		return $this;
	}

	public function setBaseUri( $baseUri ) {
		\UString::doStartWith( $baseUri, '/' );
		\UString::doNotEndWith( $baseUri, '/' );
		$this->baseUri = $baseUri;
		return $this;
	}



	/*************************************************************************
	  INITIALIZATION             
	 *************************************************************************/
	public function __construct( $extensions, $baseUri, $platform ) {
		$this->extensions = $extensions;
		$this->setBaseUri( $baseUri );
		$this->platform   = $platform;
	}

	public function initialize( ) {
		$settings = ( new \Stack\Setting )
			->clearCache( )
			->parse( $this );

		// Display errors
		$displayErrors = 0;
		if ( $settings->getAsBoolean( 'error.display_errors' ) ) {
			$displayErrors = 1;
		}
		ini_set( 'display_errors', $displayErrors );

		// Level reporting
		$level = $settings->get( 'error.error_reporting' );
		if ( ! is_numeric ( $level ) ) {
			$level = 0;
		}
		error_reporting( $level );
		
		$this->initialized = TRUE;
		return $this;
	}
}

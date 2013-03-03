<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

class Server {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $application;
	public static $autoloader;
	protected $applications = [ ];
	protected $platforms = [ ];
	public $namespaces = [ ];



	/*************************************************************************
	  CONSTRUCTOR
	 *************************************************************************/
	public function __construct( ) {
		if ( ! headers_sent() && session_id( ) === '' ) {
			session_start( );
		}
	}



	/*************************************************************************
	  SETTER METHODS                   
	 *************************************************************************/
	public function addApplication( $namespace, $listenings = NULL ) {
		if ( is_null( $listenings ) ) {
			$listenings = $this->getDefaultBaseUri( );
		}
		$this->doFormatListenings( $listenings );
		$this->applications[ $namespace ] = $listenings;
		return $this;
	}

	public function addPlatform( $platform_name, $listenings = '/' ) {
		$this->doFormatListenings( $listenings );
		$this->platforms[ $platform_name ] = $listenings;
		return $this;
	}

	protected function doFormatListenings( &$listenings ) {
		\UArray::doConvertToArray( $listenings );
		$listenings = ( new \Staq\Url )->fromArray( $listenings );
	}



	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function createApplication( $namespace = 'Staq\Core\Ground', $base_uri = NULL, $platform = 'prod' ) {
		if ( empty( $base_uri ) ) {
			$base_uri = $this->getDefaultBaseUri( );
		}
		$extensions = $this->findExtensions( $namespace );
		if ( ! is_null( static::$autoloader ) ) {
			spl_autoload_unregister( array( static::$autoloader, 'autoload' ) );
		}
		static::$autoloader = new \Staq\Autoloader( $extensions );
		spl_autoload_register( array( static::$autoloader, 'autoload' ) );
		static::$application = new \Stack\Application( $extensions, $base_uri, $platform );
		static::$application->initialize( );
		return static::$application;
	}

	public function launch( ) {
		return $this->launchCurrentApplication( );
	}

	public function launchCurrentApplication( ) {
	 	$this->addDefaultEnvironment( );
		$base_uri  = '';
		$request   = ( new \Staq\Url )->byServer( );
		$platform  = $this->getCurrentPlatform( $request, $base_uri );
		$namespace = $this->getCurrentApplicationName( $request, $base_uri );
		\UString::doStartWith( $base_uri, '/' );
		return $this->createApplication( $namespace, $base_uri, $platform );
	}



	/*************************************************************************
	  APPLICATION SWITCH SETTINGS             
	 *************************************************************************/
	protected function getCurrentPlatform( $request, &$base_uri ) {
		foreach ( $this->platforms as $platform => $listenings ) {
			foreach ( $listenings as $listening ) {
				if ( $listening->match( $request ) ) {
					$base_uri .= $listening->uri;
					\UString::doNotEndWith( $base_uri, '/' );
					return $platform;
				}
			}
		}
	}

	protected function getCurrentApplicationName( $request, &$base_uri ) {
		foreach ( $this->applications as $application => $listenings ) {
			foreach ( $listenings as $listening ) {
				$listening->uri = $base_uri . $listening->uri;
				if ( $listening->match( $request ) ) {
					$base_uri = $listening->uri;
					\UString::doNotEndWith( $base_uri, '/' );
					return $application;
				}
			}
		}
	}

	protected function getDefaultBaseUri( ) {
		$base_uri = NULL;
		if ( isset( $_SERVER[ 'DOCUMENT_ROOT' ] ) && isset( $_SERVER[ 'SCRIPT_FILENAME' ] ) ) {
			if ( \UString::isStartWith( $_SERVER[ 'SCRIPT_FILENAME' ], $_SERVER[ 'DOCUMENT_ROOT' ] ) ) {
				$base_uri = \UString::notStartWith( dirname( $_SERVER[ 'SCRIPT_FILENAME' ] ), $_SERVER[ 'DOCUMENT_ROOT' ] );
			}
		}
		if ( empty( $base_uri ) ) {
			$base_uri = '/';
		}
		return $base_uri;
	}

	protected function addDefaultEnvironment( ) {
	 	$this->addApplication( 'Staq\Core\Ground' );
	 	$this->addPlatform( 'prod', '/' );
	}



	/*************************************************************************
	  EXTENSIONS PARSING SETTINGS             
	 *************************************************************************/
	protected function findExtensions( $namespace ) {
		$this->initializeNamespaces( );
		$files = [  ];
		$namespaces = [ $namespace, 'Staq\Core\Ground' ];
		$new_namespaces = [ $namespace, 'Staq\Core\Ground' ];
		while ( count( $new_namespaces ) > 0 ) {
			$new_extensions = $this->formatExtensionsFromNamespaces( $new_namespaces );
			foreach ( $new_extensions as $extension ) {
				$files[ ] = $extension . '/setting/Application.ini';
			}
			$ini = ( new \Pixel418\Iniliq\Parser )->parse( array_reverse( $files ) );
			$fetch_namespaces = array_reverse( $ini->getAsArray( 'extension.list' ) );
			$new_namespaces = array_diff( $fetch_namespaces, $namespaces );
			$namespaces = array_merge( $namespaces, $fetch_namespaces );
		}
		$namespaces = \UArray::reverseMergeUnique( $namespaces, [ ] );
		return $this->formatExtensionsFromNamespaces( $namespaces );
	}

	protected function initializeNamespaces( ) {
		if ( empty( $this->namespaces ) ) {
			if ( \UString::has( __DIR__, '/vendor/' ) ) {
				$base_dir = \UString::substrBeforeLast( __DIR__, '/vendor/' );
			} else {
				$base_dir = __DIR__ . '/../..';
			}
			$this->namespaces = ( require( $base_dir . '/vendor/composer/autoload_namespaces.php' ) );
		}
	}

	protected function formatExtensionsFromNamespaces( $extensions ) {
		\UArray::doConvertToArray( $extensions );
		foreach ( $extensions as $key => $namespace ) {
			$this->doFormatExtensionNamespace( $namespace );
			$path = $this->findExtensionPath( $namespace );
			unset( $extensions[ $key ] );
			if ( ! empty( $path ) ) {
				$extensions[ $namespace ] = $path;
			}
		}
		return $extensions;
	}

	protected function findExtensionPath( $namespace ) {
		foreach ( $this->namespaces as $base_namespace => $base_paths ) {
            if ( \UString::isStartWith( $namespace, $base_namespace ) ) {
				\UArray::doConvertToArray( $base_paths );
				foreach ( $base_paths as $base_path ) {
        			$path = str_replace( '\\', DIRECTORY_SEPARATOR, $namespace );
                	\UString::doEndWith( $base_path, DIRECTORY_SEPARATOR );
	            	$path = $base_path . $path;
	                if ( is_dir( $path ) ) {
	                    return $path;
	                }
	        	}
            }
        }
	}

	protected function doFormatExtensionNamespace( &$namespace ) {
		$namespace = str_replace( DIRECTORY_SEPARATOR, '\\', $namespace );
		\UString::doNotStartWith( $namespace, '\\' );
		\UString::doNotEndWith( $namespace, '\\' );
	}
}

if ( ! defined( 'HTML_EOL' ) ) {
	define( 'HTML_EOL', '<br/>' . PHP_EOL );
}

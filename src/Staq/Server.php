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

	public function addPlatform( $platformName, $listenings = '/' ) {
		$this->doFormatListenings( $listenings );
		$this->platforms[ $platformName ] = $listenings;
		return $this;
	}

	protected function doFormatListenings( &$listenings ) {
		\UArray::doConvertToArray( $listenings );
		$listenings = ( new \Staq\Url )->fromArray( $listenings );
	}



	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function createApplication( $namespace = 'Staq\Core\Ground', $baseUri = NULL, $platform = NULL ) {
		if ( empty( $baseUri ) ) {
			$baseUri = $this->getDefaultBaseUri( );
		}
		if ( empty( $platform ) ) {
			$platform = 'prod';
			if ( \Staq\Util::isCli( ) ) {
				if ( ! isset( $argv[ 1 ] ) ) {
					echo 'You must specify a platform.' . PHP_EOL;
					echo 'Ex: ' . $argv[ 0 ] . ' local' . PHP_EOL;
					die;
				}
				$platform = $argv[ 1 ];
			}
		}
		$extensions = $this->findExtensions( $namespace );
		if ( ! is_null( static::$autoloader ) ) {
			spl_autoload_unregister( array( static::$autoloader, 'autoload' ) );
		}
		static::$autoloader = new \Staq\Autoloader( $extensions );
		spl_autoload_register( array( static::$autoloader, 'autoload' ) );
		static::$application = new \Stack\Application( $extensions, $baseUri, $platform );
		static::$application->initialize( );
		return static::$application;
	}

	public function launch( ) {
		return $this->launchCurrentApplication( );
	}

	public function launchCurrentApplication( ) {
	 	$this->addDefaultEnvironment( );
		$baseUri  = '';
		$request   = ( new \Staq\Url )->byServer( );
		$platform  = $this->getCurrentPlatform( $request, $baseUri );
		$namespace = $this->getCurrentApplicationName( $request, $baseUri );
		\UString::doStartWith( $baseUri, '/' );
		return $this->createApplication( $namespace, $baseUri, $platform );
	}



	/*************************************************************************
	  APPLICATION SWITCH SETTINGS             
	 *************************************************************************/
	protected function getCurrentPlatform( $request, &$baseUri ) {
		foreach ( $this->platforms as $platform => $listenings ) {
			foreach ( $listenings as $listening ) {
				if ( $listening->match( $request ) ) {
					$baseUri .= $listening->uri;
					\UString::doNotEndWith( $baseUri, '/' );
					return $platform;
				}
			}
		}
	}

	protected function getCurrentApplicationName( $request, &$baseUri ) {
		foreach ( $this->applications as $application => $listenings ) {
			foreach ( $listenings as $listening ) {
				$listening->uri = $baseUri . $listening->uri;
				if ( $listening->match( $request ) ) {
					$baseUri = $listening->uri;
					\UString::doNotEndWith( $baseUri, '/' );
					return $application;
				}
			}
		}
	}

	protected function getDefaultBaseUri( ) {
		$baseUri = NULL;
		if ( isset( $_SERVER[ 'DOCUMENT_ROOT' ] ) && isset( $_SERVER[ 'SCRIPT_FILENAME' ] ) ) {
			if ( \UString::isStartWith( $_SERVER[ 'SCRIPT_FILENAME' ], $_SERVER[ 'DOCUMENT_ROOT' ] ) ) {
				$baseUri = \UString::notStartWith( dirname( $_SERVER[ 'SCRIPT_FILENAME' ] ), $_SERVER[ 'DOCUMENT_ROOT' ] );
			}
		}
		if ( empty( $baseUri ) ) {
			$baseUri = '/';
		}
		return $baseUri;
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
		$newNamespaces = [ $namespace, 'Staq\Core\Ground' ];
		while ( count( $newNamespaces ) > 0 ) {
			$newExtensions = $this->formatExtensionsFromNamespaces( $newNamespaces );
			foreach ( $newExtensions as $extension ) {
				$files[ ] = $extension . '/setting/Application.ini';
			}
			$ini = ( new \Pixel418\Iniliq\Parser )->parse( array_reverse( $files ) );
			$fetchNamespaces = array_reverse( $ini->getAsArray( 'extension.list' ) );
			$newNamespaces = array_diff( $fetchNamespaces, $namespaces );
			$namespaces = array_merge( $namespaces, $fetchNamespaces );
		}
		$namespaces = \UArray::reverseMergeUnique( $namespaces, [ ] );
		return $this->formatExtensionsFromNamespaces( $namespaces );
	}

	protected function initializeNamespaces( ) {
		if ( empty( $this->namespaces ) ) {
			if ( \UString::has( __DIR__, '/vendor/' ) ) {
				$baseDir = \UString::substrBeforeLast( __DIR__, '/vendor/' );
			} else {
				$baseDir = __DIR__ . '/../..';
			}
			$this->namespaces = ( require( $baseDir . '/vendor/composer/autoload_namespaces.php' ) );
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
		foreach ( $this->namespaces as $baseNamespace => $basePaths ) {
            if ( \UString::isStartWith( $namespace, $baseNamespace ) ) {
				\UArray::doConvertToArray( $basePaths );
				foreach ( $basePaths as $basePath ) {
        			$path = str_replace( '\\', DIRECTORY_SEPARATOR, $namespace );
                	\UString::doEndWith( $basePath, DIRECTORY_SEPARATOR );
	            	$path = $basePath . $path;
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

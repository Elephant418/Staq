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
	  SETTER METHODS                   
	 *************************************************************************/
	public function add_application( $namespace, $listenings = NULL ) {
		if ( is_null( $listenings ) ) {
			$listenings = $this->get_default_base_uri( );
		}
		$this->do_format_listenings( $listenings );
		$this->applications[ $namespace ] = $listenings;
		return $this;
	}

	public function add_platform( $platform_name, $listenings = '/' ) {
		$this->do_format_listenings( $listenings );
		$this->platforms[ $platform_name ] = $listenings;
		return $this;
	}

	protected function do_format_listenings( &$listenings ) {
		\UArray::do_convert_to_array( $listenings );
		$listenings = ( new \Staq\Url )->from_array( $listenings );
	}



	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function create_application( $namespace = 'Staq\Core\Ground', $base_uri = NULL, $platform = 'prod' ) {
		if ( empty( $base_uri ) ) {
			$base_uri = $this->get_default_base_uri( );
		}
		$extensions = $this->find_extensions( $namespace );
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
		return $this->launch_current_application( );
	}

	public function launch_current_application( ) {
	 	$this->add_default_environment( );
		$base_uri  = '';
		$request   = ( new \Staq\Url )->by_server( );
		$platform  = $this->get_current_platform( $request, $base_uri );
		$namespace = $this->get_current_application_name( $request, $base_uri );
		\UString::do_start_with( $base_uri, '/' );
		return $this->create_application( $namespace, $base_uri, $platform );
	}



	/*************************************************************************
	  APPLICATION SWITCH SETTINGS             
	 *************************************************************************/
	protected function get_current_platform( $request, &$base_uri ) {
		foreach ( $this->platforms as $platform => $listenings ) {
			foreach ( $listenings as $listening ) {
				if ( $listening->match( $request ) ) {
					$base_uri .= $listening->uri;
					\UString::do_not_end_with( $base_uri, '/' );
					return $platform;
				}
			}
		}
	}

	protected function get_current_application_name( $request, &$base_uri ) {
		foreach ( $this->applications as $application => $listenings ) {
			foreach ( $listenings as $listening ) {
				$listening->uri = $base_uri . $listening->uri;
				if ( $listening->match( $request ) ) {
					$base_uri = $listening->uri;
					\UString::do_not_end_with( $base_uri, '/' );
					return $application;
				}
			}
		}
	}

	protected function get_default_base_uri( ) {
		$base_uri = NULL;
		if ( isset( $_SERVER[ 'DOCUMENT_ROOT' ] ) && isset( $_SERVER[ 'SCRIPT_FILENAME' ] ) ) {
			if ( \UString::is_start_with( $_SERVER[ 'SCRIPT_FILENAME' ], $_SERVER[ 'DOCUMENT_ROOT' ] ) ) {
				$base_uri = \UString::not_start_with( dirname( $_SERVER[ 'SCRIPT_FILENAME' ] ), $_SERVER[ 'DOCUMENT_ROOT' ] );
			}
		}
		if ( empty( $base_uri ) ) {
			$base_uri = '/';
		}
		return $base_uri;
	}

	protected function add_default_environment( ) {
	 	$this->add_application( 'Staq\Core\Ground' );
	 	$this->add_platform( 'prod', '/' );
	}



	/*************************************************************************
	  EXTENSIONS PARSING SETTINGS             
	 *************************************************************************/
	protected function find_extensions( $namespace ) {
		$this->initialize_namespaces( );
		$files = [  ];
		$namespaces = [ $namespace, 'Staq\Core\Ground' ];
		$new_namespaces = [ $namespace, 'Staq\Core\Ground' ];
		while ( count( $new_namespaces ) > 0 ) {
			$new_extensions = $this->format_extensions_from_namespaces( $new_namespaces );
			foreach ( $new_extensions as $extension ) {
				$files[ ] = $extension . '/setting/Application.ini';
			}
			$ini = ( new \Pixel418\Iniliq\Parser )->parse( array_reverse( $files ) );
			$fetch_namespaces = array_reverse( $ini->get_as_array( 'extension.list' ) );
			$new_namespaces = array_diff( $fetch_namespaces, $namespaces );
			$namespaces = array_merge( $namespaces, $fetch_namespaces );
		}
		$namespaces = \UArray::reverse_merge_unique( $namespaces, [ ] );
		return $this->format_extensions_from_namespaces( $namespaces );
	}

	protected function initialize_namespaces( ) {
		if ( empty( $this->namespaces ) ) {
			if ( \UString::has( __DIR__, '/vendor/' ) ) {
				$base_dir = \UString::substr_before_last( __DIR__, '/vendor/' );
			} else {
				$base_dir = __DIR__ . '/../..';
			}
			$this->namespaces = ( require( $base_dir . '/vendor/composer/autoload_namespaces.php' ) );
		}
	}

	protected function format_extensions_from_namespaces( $extensions ) {
		\UArray::do_convert_to_array( $extensions );
		foreach ( $extensions as $key => $namespace ) {
			$this->do_format_extension_namespace( $namespace );
			$path = $this->find_extension_path( $namespace );
			unset( $extensions[ $key ] );
			if ( ! empty( $path ) ) {
				$extensions[ $namespace ] = $path;
			}
		}
		return $extensions;
	}

	protected function find_extension_path( $namespace ) {
		foreach ( $this->namespaces as $base_namespace => $base_paths ) {
            if ( \UString::is_start_with( $namespace, $base_namespace ) ) {
				\UArray::do_convert_to_array( $base_paths );
				foreach ( $base_paths as $base_path ) {
        			$path = str_replace( '\\', DIRECTORY_SEPARATOR, $namespace );
                	\UString::do_end_with( $base_path, DIRECTORY_SEPARATOR );
	            	$path = $base_path . $path;
	                if ( is_dir( $path ) ) {
	                    return $path;
	                }
	        	}
            }
        }
	}

	protected function do_format_extension_namespace( &$namespace ) {
		$namespace = str_replace( DIRECTORY_SEPARATOR, '\\', $namespace );
		\UString::do_not_start_with( $namespace, '\\' );
		\UString::do_not_end_with( $namespace, '\\' );
	}
}

if ( ! defined( 'HTML_EOL' ) ) {
	define( 'HTML_EOL', '<br/>' . PHP_EOL );
}

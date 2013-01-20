<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

class Server {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $application;
	public static $autoloader;
	public $namespaces = [ ];




	/*************************************************************************
	  CONSTRUCTOR METHODS             
	 *************************************************************************/
	public function __construct( ) {
		if ( \UString::has( __DIR__, '/vendor/' ) ) {
			$base_dir = \UString::substr_before_last( __DIR__, '/vendor/' );
		} else {
			$base_dir = __DIR__ . '/../..';
		}
		$this->namespaces = ( require( $base_dir . '/vendor/composer/autoload_namespaces.php' ) );
	}




	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function create_application( $path = 'Staq\Core\Ground', $root_uri = '/', $platform = 'prod' ) {
		$extensions = $this->find_extensions( $path );
		if ( ! is_null( self::$autoloader ) ) {
			spl_autoload_unregister( array( self::$autoloader, 'autoload' ) );
		}
		self::$autoloader = new \Staq\Autoloader( $extensions );
		spl_autoload_register( array( self::$autoloader, 'autoload' ) );
		self::$application = new \Stack\Application( $extensions, $path, $root_uri, $platform );
		return self::$application;
	}



	/*************************************************************************
	  EXTENSIONS PARSING SETTINGS             
	 *************************************************************************/
	protected function find_extensions( $namespace ) {
		$files = [  ];
		$namespaces = [ $namespace, 'Staq\Core\Ground' ];
		$new_namespaces = [ $namespace, 'Staq\Core\Ground' ];
		while ( count( $new_namespaces ) > 0 ) {
			$new_extensions = $this->format_extensions_from_namespaces( $new_namespaces );
			foreach ( $new_extensions as $extension ) {
				$files[ ] = $extension . '/setting/application.ini';
			}
			$ini = ( new \Pixel418\Iniliq\Parser )->parse( array_reverse( $files ) );
			$fetch_namespaces = array_reverse( $ini->get_as_array( 'extension.list' ) );
			$new_namespaces = array_diff( $fetch_namespaces, $namespaces );
			$namespaces = array_merge( $namespaces, $fetch_namespaces );
		}
		$namespaces = \UArray::reverse_merge_unique( $namespaces, [ ] );
		return $this->format_extensions_from_namespaces( $namespaces );
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
	            	$path = $base_path . DIRECTORY_SEPARATOR . $path;
	                if ( is_dir( $path ) ) {
	                    return $path . DIRECTORY_SEPARATOR;
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

<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

class Server {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $application;
	public static $autoloader;




	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function create_application( $path = 'staq/core/ground', $root_uri = '/', $platform = 'prod' ) {
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
	protected function find_extensions( $name ) {
		$extensions = [ $name ];
		$this->find_extensions_recursive( $name, $extensions );
		$extensions = $this->format_extensions_from_names( $extensions );
		return $extensions;
	}

	protected function find_extensions_recursive( $extension, &$extensions, $disabled = [ ] ) {
		$added_extensions = $this->find_extensions_parse_setting_file( $extension, $disabled );
		$old_extensions = $extensions;
		$extensions = \UArray::reverse_merge_unique( $extensions, $added_extensions );
		foreach ( array_diff( $added_extensions, $old_extensions ) as $added_extension ) {
			$this->find_extensions_recursive( $added_extension, $extensions, $disabled );
		}
	}

	protected function find_extensions_parse_setting_file( $extension, &$disabled ) {
		$added_extensions = [ ];
		if ( $setting_file_path = $this->find_extension_path( $extension ) ) {
			$setting_file_path .= '/setting/application.ini';
			if ( is_file( $setting_file_path ) ) {
				$setting = parse_ini_file( $setting_file_path, TRUE );
				if ( isset( $setting[ 'extension_list' ] ) ) {
					$ext = $setting[ 'extension_list' ];
					if ( isset( $ext[ 'enabled' ] ) && is_array( $ext[ 'enabled' ] ) ) {
						$added_extensions = array_diff( $ext[ 'enabled' ], $disabled );
					}
					if ( isset( $ext[ 'disabled' ] ) && is_array( $ext[ 'disabled' ] ) ) {
						$disabled = \UArray::merge_unique( $disabled, $ext[ 'disabled' ] );
					}
				}
			}
		}
		// Default value for extension without configuration 
		if ( empty( $added_extensions ) ) {
			$added_extensions = [ 'staq/core/ground' ];
		}
		return $added_extensions;
	}

	protected function format_extensions_from_names( $extensions ) {
		foreach ( $extensions as $key => $name ) {
			$this->do_format_extension_name( $name );
			$extensions[ $key ]                = [ ];
			$extensions[ $key ][ 'name' ]      = $name;
			$extensions[ $key ][ 'namespace' ] = \Staq\Util\string_path_to_namespace( $name );
			$extensions[ $key ][ 'path' ]      = $this->find_extension_path( $name );
		}
		return $extensions;
	}

	protected function find_extension_path( $name ) {
		if ( \UString::is_start_with( $name, 'staq/' ) ) {
			$name = 'pixel418/' . $name;
		}
		if ( is_dir( \Staq\ROOT_PATH . $name ) ) {
			return \Staq\ROOT_PATH . $name . '/';
		}
		$name = explode( '/', $name );
		$name[ 1 ] .= '/src';
		$name = implode( '/', $name );
		if ( is_dir( \Staq\VENDOR_ROOT_PATH . $name ) ) {
			return \Staq\VENDOR_ROOT_PATH . $name . '/';
		}
	}

	protected function do_format_extension_name( &$name ) {
		$name = str_replace( '\\', '/', $name );
		\UString::do_not_start_with( $name, '/' );
		\UString::do_not_end_with( $name, '/' );
	}
}

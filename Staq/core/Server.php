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
	public function create_application( $path = 'Staq/core/ground', $root_uri = '/', $platform = 'prod' ) {
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
	protected function find_extensions( $path ) {
		$extensions = [ $path ];
		$this->find_extensions_recursive( $path, $extensions );
		return $extensions;
	}

	protected function find_extensions_recursive( $extension, &$extensions, $disabled = [ ] ) {
		$added_extensions = $this->find_extensions_parse_setting_file( $extension, $disabled );
		$old_extensions = $extensions;
		$extensions = \UArray\reverse_merge_unique( $extensions, $added_extensions );
		foreach ( array_diff( $added_extensions, $old_extensions ) as $added_extension ) {
			$this->find_extensions_recursive( $added_extension, $extensions, $disabled );
		}
	}

	protected function find_extensions_parse_setting_file( $extension, &$disabled ) {
		$added_extensions = [ ];
		$setting_file_path = STAQ_ROOT_PATH . $extension . '/setting/application.ini';
		if ( is_file( $setting_file_path ) ) {
			$setting = parse_ini_file( $setting_file_path, TRUE );
			if ( isset( $setting[ 'extension_list' ] ) ) {
				$ext = $setting[ 'extension_list' ];
				if ( isset( $ext[ 'enabled' ] ) && is_array( $ext[ 'enabled' ] ) ) {
					$added_extensions = array_diff( $ext[ 'enabled' ], $disabled );
				}
				if ( isset( $ext[ 'disabled' ] ) && is_array( $ext[ 'disabled' ] ) ) {
					$disabled = \UArray\merge_unique( $disabled, $ext[ 'disabled' ] );
				}
			}
		}
		// Default value for extension without configuration 
		if ( empty( $added_extensions ) ) {
			$added_extensions = [ 'Staq/core/ground' ];
		}
		return $added_extensions;
	}
}

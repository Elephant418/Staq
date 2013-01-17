<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Ground\Stack;

class Setting {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	public static $cache = [ ];


	/*************************************************************************
	  CACHE METHODS                   
	 *************************************************************************/
	public function clear_cache( ) {
		self::$cache = [ ];
	}



	/*************************************************************************
	  PARSE METHODS              
	 *************************************************************************/
	public function parse( $setting_file_name ) {
		$this->do_format_setting_file_name( $setting_file_name );
		if ( TRUE || ! isset( self::$cache[ $setting_file_name ] ) ) {
			$file_paths = $this->get_file_paths( $setting_file_name );
			$ini = ( new \Pixel418\Iniliq\Parser )->parse( $file_paths );
			self::$cache[ $setting_file_name ] = $ini;
		}
		return self::$cache[ $setting_file_name ];
	}

	protected function do_format_setting_file_name( &$mixed ) {
		if ( \Staq\Util::is_stack_object( $mixed ) ) {
			$mixed = \Staq\Util::string_namespace_to_path( \Staq\Util::stack_query( $mixed ) );
		}
	}

	protected function get_file_paths( $full_setting_file_name ) {
		$file_names = $this->get_file_names( $full_setting_file_name );
		$platform_name = \Staq\Application::get_platform( );
		$file_paths = [ ];
		foreach ( \Staq\Application::get_extensions( ) as $extension ) {
			foreach( $file_names as $file_name ) {
				if ( $platform_name ) {
					$file_name .= '.' . $platform_name;
				}
				while ( $file_name ) {
					$file_paths[ ] = $extension['path'] . 'setting/' . $file_name . '.ini';
					$file_name = \UString::substr_before_last( $file_name, '.' );
				}
			}
		}
		return array_reverse( $file_paths );
	}

	protected function get_file_names( $file_name ) {
		$file_names = [ ];
		do {
			$file_names[ ] = $file_name;
			$file_name = \UString::substr_before_last( $file_name, '/' );
		} while( ! empty( $file_name ) );
		return $file_names;
	}
}

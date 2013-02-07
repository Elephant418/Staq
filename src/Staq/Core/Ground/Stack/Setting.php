<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Ground\Stack;

class Setting {



	/*************************************************************************
	  ATTRIBUTES         
	 *************************************************************************/
	static public $cache = [ ]; 



	/*************************************************************************
	  CACHE METHODS              
	 *************************************************************************/
	public function clear_cache( ) {
		static::$cache = [ ];
		return $this;
	}



	/*************************************************************************
	  PARSE METHODS              
	 *************************************************************************/
	public function parse( $setting_file_name ) {
		$stack = FALSE;
		if ( \Staq\Util::is_stack( $setting_file_name ) ) {
			$stack = $setting_file_name;
			$this->do_format_setting_file_name( $setting_file_name );
		}
		if ( ! isset( static::$cache[ $setting_file_name ] ) ) {
			$file_paths = $this->get_file_paths( $setting_file_name );
			if ( $stack ) {
				foreach( \Staq\Util::stack_definition( $stack ) as $class ) {
					if ( isset( $class::$setting ) ) {
						array_unshift( $file_paths, $class::$setting );
					}
				}
			}
			static::$cache[ $setting_file_name ] = ( new \Pixel418\Iniliq\Parser )->parse( $file_paths );
		}
		return static::$cache[ $setting_file_name ];
	}

	protected function do_format_setting_file_name( &$mixed ) {
		$mixed = \Staq\Util::stack_query( $mixed );
		$mixed = \Staq\Util::string_namespace_to_path( $mixed );
		$mixed = strtolower( $mixed );
	}

	protected function get_file_paths( $full_setting_file_name ) {
		$file_names = $this->get_file_names( $full_setting_file_name );
		$platform_name = \Staq::App()->get_platform( );
		$file_paths = [ ];
		foreach ( \Staq::App()->get_extensions( ) as $extension ) {
			foreach( $file_names as $file_name ) {
				if ( $platform_name ) {
					$file_name .= '.' . $platform_name;
				}
				while ( $file_name ) {
					$path = realpath( $extension . '/setting/' . $file_name . '.ini' );
					if ( $path ) {
						$file_paths[ ] = $path;
					}
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

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
	public function parse( $mixed ) {
		if ( \Staq\Util::is_stack( $mixed ) ) {
			return $this->parse_from_stack( $mixed );
		}
		return $this->parse_from_string( $mixed );
	}

	protected function parse_from_stack( $stack ) {
		$setting_file_name = $this->get_setting_file_name_from_stack( $stack );
		if ( ! isset( static::$cache[ $setting_file_name ] ) ) {
			$file_paths = $this->get_file_paths( $setting_file_name );
			foreach( \Staq\Util::stack_definition( $stack ) as $class ) {
				if ( isset( $class::$setting ) ) {
					array_unshift( $file_paths, $class::$setting );
				}
			}
			$settings = ( new \Pixel418\Iniliq\Parser )->parse( $file_paths );
			static::$cache[ $setting_file_name ] = $settings;
		}
		return static::$cache[ $setting_file_name ];
	}

	protected function parse_from_string( $setting_file_name ) {
		\UString::do_substr_before( $setting_file_name, '.' );
		return $this->parse_from_stack( 'Stack\\' . $setting_file_name );
	}

	protected function get_setting_file_name_from_stack( $stack ) {
		$setting_file_name = \Staq\Util::stack_query( $stack );
		return \Staq\Util::string_namespace_to_path( $setting_file_name );
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

<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Kernel\Object;

class Settings {


	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	private $extensions = [ ];
	private $file_names = [ ];
	public $settings = [ ];
	public static $file_parsed = [ ];



	/*************************************************************************
	  CONSTRUCTOR                 
	 *************************************************************************/
	public function name( ) {
		return $this->type;
	}



	/*************************************************************************
	  CONSTRUCTOR                 
	 *************************************************************************/
	public function by_file_type( $file_type, $file_name ) {
		$file_name = \Supersoniq\format_to_path( strtolower( $file_name ) );
		$file_names = [ ];
		do {
			$file_names[ ] = $file_type . '/' . $file_name;
			$file_name = \Supersoniq\substr_before_last( $file_name, '/' );
		} while( ! empty( $file_name ) );
		$this->by_file( $file_names );
		return $this->load( );
	}
	public function by_file( $file_names ) {
		\Supersoniq\must_be_array( $file_names );
		$this->file_names = $file_names;
		return $this->load( );
	}
	public function load( ) {
		if ( empty( $this->extensions ) ) {
			$this->extensions = \Supersoniq::$EXTENSIONS;
		}
		$this->settings = $this->parse_files( );
		return $this;
	}



	/*************************************************************************
	  SETTINGS METHODS                   
	 *************************************************************************/
	public function extension( $extensions ) {
		\Supersoniq\must_be_array( $extensions );
		$this->extensions = $extensions;
		return $this;
	}



	/*************************************************************************
	  ACCESSOR METHODS                   
	 *************************************************************************/
	public function get( $section, $property, $default = NULL ) {
		foreach ( $this->settings as $data ) {
			if ( isset( $data[ $section ][ $property ] ) ) {
				return $data[ $section ][ $property ];
			}
		}
		return $default;
	}

	public function get_list( $property, $initial = [ ], $order_from_bottom = FALSE  ) {
		$disabled = [ ];
		$enabled = $initial;
		foreach ( $this->settings as $data ) {
			if ( isset( $data[ $property ][ 'disabled' ] ) ) {
				$new = $data[ $property ][ 'disabled' ];
				$disabled = \Supersoniq\array_merge_unique( $disabled, $new );
			}
			if ( isset( $data[ $property ][ 'enabled' ] ) ) {
				$new = array_diff( $data[ $property ][ 'enabled' ], $disabled );
				$enabled  = \Supersoniq\array_merge_unique( $enabled, $new, $order_from_bottom );
			}
		}
		return $enabled;
	}

	public function get_array( $section, $property = NULL ) {
		if ( ! is_null( $property ) ) {
			return $this->get_section_property_array( $section, $property );
		}
		return $this->get_section( $section );
	}

	private function get_section( $section ) {
		$array = [ ];
		foreach ( $this->settings as $data ) {
			if ( isset( $data[ $section ] ) ) {
				foreach ( $data[ $section ] as $name => $value ) {
					if ( ! isset( $array[ $name ] ) ) {
						$array[ $name ] = $value;
					}
				}
			}
		}
		return $array;
	}

	private function get_section_property_array( $section, $property ) {
		$array = [ ];
		foreach ( $this->settings as $data ) {
			if ( isset( $data[ $section ][ $property ] ) ) {
				$elements = $data[ $section ][ $property ];
				\Supersoniq\must_be_array( $elements );
				$array = array_merge( $array, $elements );
			}
		}
		return $array;
	}

	public function has( $section, $property ) {
		foreach ( $this->settings as $data ) {
			if ( isset( $data[ $section ][ $property ] ) ) {
				return TRUE;
			}
		}
		return FALSE;
	}



	/*************************************************************************
	  FILE METHODS                   
	 *************************************************************************/
	private function file_paths( ) {
		$file_paths = [ ];
		foreach ( $this->extensions as $extension ) {
			foreach( $this->file_names as $file_name ) {
				if ( \Supersoniq::$PLATFORM_NAME ) {
					$file_name .= '.' . \Supersoniq::$PLATFORM_NAME;
				}
				while ( $file_name ) {
					$file_paths[ ] = $extension . '/settings/' . $file_name . '.ini';
					$file_name = \Supersoniq\substr_before_last( $file_name, '.' );
				}
			}
		}
		return $file_paths;
	}

	private function parse_files( ) {
		$datas = [ ];
		foreach ( $this->file_paths( ) as $file_path ) {
			if ( isset( self::$file_parsed[ $file_path ] ) ) {
				$datas[ $file_path ] = self::$file_parsed[ $file_path ];
			} else {
				$absolute_file_path = SUPERSONIQ_ROOT_PATH . $file_path;
				if ( is_file( $absolute_file_path ) ) {
					$datas[ $file_path ] = parse_ini_file( $absolute_file_path, TRUE );
				} else {
					$datas[ $file_path ] = [ ];
				}
				self::$file_parsed[ $file_path ] = $datas[ $file_path ];
			}
		}
		return $datas;
	}
}

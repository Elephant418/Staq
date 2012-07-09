<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq;

class Autoloader {


	/*************************************************************************
	  CONSTRUCTOR                   
	 *************************************************************************/
        public function init( ) {
		spl_autoload_register( array( $this, 'loader' ) );
        }


	/*************************************************************************
	  PRIVATE METHODS                   
	 *************************************************************************/
        private function loader( $absolute_class_name ) {
		// echo $absolute_class_name . '<br>' . PHP_EOL;
		$parts = array_reverse( explode( '\\', $absolute_class_name ) );
		$class_name = $parts[ 0 ];
		$class_type = 'Object';
		if ( isset( $parts[ 1 ] ) ) {
			$class_type = $parts[ 1 ];
		}

		// Specific Module
		if ( isset( $parts[ 2 ] ) ) {
			$module = implode( '\\', array_slice( array_reverse( $parts ), 0, -2 ) );
			if ( ! in_array( $module, Application::$modules ) ) {
				throw new \Exception( 'Unknown module "' . $module . '"' );
			}
			if ( $this->load_absolute_class_name( $module, $class_type, $class_name ) ) {
				return $absolute_class_name;
			}
			throw new \Exception( 'Unknown absolute class name "' . $absolute_class_name . '"' );

		// Automatic Module
		} else {
			// Short name for an existing class 
			if ( $real_class_name = $this->load_relative_class_name( $class_type, $class_name ) ) {
				$this->create_class( $real_class_name, $absolute_class_name );
				return $real_class_name;

			// Unexisting class created on the fly
			} else if ( isset( $parts[ 1 ] ) && $class_name != '__Base' ) {
				$real_class_name = $class_type . '\__Base';
				if ( ! class_exists( $real_class_name ) ) {
					$real_class_name = $this->loader( $real_class_name );
				}
				if ( $base_class_name = $this->autoload_create_child( $real_class_name ) ) {
					$this->create_class( $base_class_name, $absolute_class_name );
					return $base_class_name;
				}
			}
			throw new \Exception( 'Unknown relative class name "' . $absolute_class_name . '"' );
		}
	}
        private function load_relative_class_name( $class_type, $class_name ) {
		foreach ( Application::$modules as $module ) {
			if ( $this->load_absolute_class_name( $module, $class_type, $class_name ) ) {
				return $module . '\\' . $class_type . '\\' . $class_name;
			}
		}
		return FALSE;
	}
        private function load_absolute_class_name( $module, $class_type, $class_name ) {
		$module_path = Application::$modules_path[ $module ];
		$file_path   = $module_path . strtolower( $class_type ) . '/' . $class_name . '.php';
		// echo '-- ' . $file_path . '<br>';
		if ( is_file( $file_path ) ) {
			require_once( $file_path );
			return TRUE;
		}
		return FALSE;
	}
	private function create_class( $real_class_name, $new_class_name ) {
		$code = '';
		if ( contains( $new_class_name, '\\' ) ) {
			$namespace = substr_before_last( $new_class_name, '\\' );
			$code .= 'namespace ' . $namespace . ';' . PHP_EOL;
			$new_class_name = substr_after_last( $new_class_name, '\\' );
		}
		$code .= 'class ' . $new_class_name . ' extends \\' . $real_class_name . ' { }' . PHP_EOL;
		// echo $code . PHP_EOL;
		eval( $code );
	}
	private function autoload_create_child( $class_name ) {
		if ( $class_name == 'Exception' ) {
			return '\\Exception';
		}
		return $this->get_user_prop( '\\' . $class_name , 'autoload_create_child' );
	}
	private function get_user_prop( $class_name, $property ) {
		if( ! class_exists( $class_name ) ) {
			return NULL;
		}
		if( ! property_exists( $class_name, $property ) ) {
			return NULL;
		}
		$class_vars = get_class_vars( $class_name );
		// echo 'property : ' . $class_vars[ $property ] . PHP_EOL;
		return $class_vars[ $property ];
	}

}

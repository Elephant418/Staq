<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

class Autoloader {

	const DEFAULT_CLASS = '__Default';



	/*************************************************************************
	  TOP-LEVEL AUTOLOAD
	 *************************************************************************/
	public function autoload( $class ) {
		if ( \Staq\Util\is_stack_class( $class ) ) {
			$this->load_stack_class( $class );
		} else if ( \Staq\Util\is_parent_stack_class( $class ) ) {
			$this->load_stack_parent_class( $class );
		}
	}


	/*************************************************************************
	  FILE CLASS MANAGEMENT             
	 *************************************************************************/
	protected function load_stack_class( $class ) {
		$stack_class = \Staq\Util\string_substr_after( $class, 'Stack\\' );
		while( $stack_class ) {
			foreach( Application::$extensions as $extension ) {
				if ( $real_class = $this->load_stack_extension_file( $stack_class, $extension ) ) {
					$this->create_alias_class( $class, $real_class );
					return TRUE;
				}
			}
			$stack_class = \Staq\Util\stack_name_pop( $stack_class );
		}

		// Empty class
		$this->create_class( $class );
	}
	protected function load_stack_extension_file( $stack, $extension ) {
		$relative_path = $extension . '/stack/' . $this->string_namespace_to_path( $stack );
		$absolute_path = STAQ_ROOT_PATH . $relative_path . '.php';
		if ( is_file( $absolute_path ) ) {
			require_once( $absolute_path );
			$real_class = $this->string_path_to_namespace( $relative_path );
			$this->check_class_loaded( $real_class );
			return $real_class;
		}
	}
	

	protected function load_stack_parent_class( $class ) {
		// TODO
	}



	/*************************************************************************
	  CLASS DECLARATION             
	 *************************************************************************/
	protected function create_alias_class( $alias, $class ) {
		return $this->create_class( $alias, $class );
	}
	protected function create_class( $class, $base_class = NULL ) {
		$namespace = \Staq\Util\string_substr_before_last( $class, '\\' );
		$name = \Staq\Util\string_substr_after_last( $class, '\\' );
		$code = '';
		if ( $namespace ) {
			$code = 'namespace ' . $namespace . ';' . PHP_EOL;
		}
		$code .= 'class ' . $name . ' ';
		if ( $base_class ) {
			$code .= 'extends ' . $base_class . ' ';
		}
		$code .= '{ }' . PHP_EOL;
		// echo $code . HTML_EOL;
		eval( $code );
	}
	protected function check_class_loaded( $class ) {
		if ( ! class_exists( $class ) ) {
			$classes = get_declared_classes( );
			$loaded_class = end( $classes );
			throw new \Stack\Exception\Wrong_Class_Definition( 'Wrong class definition: "' . $loaded_class . '" definition, but "' . $class . '" expected.' ); 
		}
	}



	/*************************************************************************
	  NAMESPACE FORMAT             
	 *************************************************************************/
	protected function string_path_to_namespace( $path, $absolute = TRUE ) {
		$namespace = implode( '\\', array_map( function( $a ) {
			return ucfirst( $a );
		}, explode( '/', $path ) ) );
		if ( $absolute ) $namespace = '\\' . $namespace;
		return $namespace;
	}
	protected static function string_namespace_to_path( $namespace, $file = TRUE ) {
		if ( $file ) {
			$parts = explode( '\\', $namespace );
			$class = array_pop( $parts );
			return strtolower( implode( '/', $parts ) ) . '/' . $class;
		}
		return str_replace( '\\', '/' , $namespace );
	}
}
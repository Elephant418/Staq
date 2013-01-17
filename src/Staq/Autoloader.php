<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

class Autoloader {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $extensions = [ ];



	/*************************************************************************
	  INITIALIZATION             
	 *************************************************************************/
	public function __construct( $extensions ) {
		$this->extensions = $extensions;
	}



	/*************************************************************************
	  TOP-LEVEL AUTOLOAD
	 *************************************************************************/
	public function autoload( $class ) {
		if ( \Staq\Util::is_stack( $class ) ) {
			$this->load_stack_class( $class );
		} else if ( \Staq\Util::is_parent_stack( $class ) ) {
			$this->load_stack_parent_class( $class );
		}
	}


	/*************************************************************************
	  FILE CLASS MANAGEMENT             
	 *************************************************************************/
	protected function load_stack_class( $class ) {
		$stack_query = \Staq\Util::stack_query( $class );
		while( $stack_query ) {
			foreach( $this->extensions as $extension ) {
				if ( $real_class = $this->get_real_class_of_stack_extension( $stack_query, $extension ) ) {
					$this->create_alias_class( $class, $real_class );
					return TRUE;
				}
			}
			$stack_query = \Staq\Util::stack_query_pop( $stack_query );
		}

		$this->create_empty_class( $class );
	}

	// "stack" is now a part of the namespace, there is no burgers left at my bakery 
	protected function get_real_class_of_stack_extension( $stack, $extension ) {
		$stack_path = \Staq\Util::string_namespace_to_class_path( $stack );
		$absolute_path = $extension['path'] . 'Stack/' . $stack_path . '.php';
		if ( is_file( $absolute_path ) ) {
			$real_class = $extension['namespace'] . '\\Stack\\' . $stack;
			return $real_class;
		}
	}
	

	protected function load_stack_parent_class( $class ) {
		$query_extension = \Staq\Util::stackable_extension( $class );
		$query = \Staq\Util::parent_stack_query( $class );
		$ready = FALSE;
		while( $query ) {
			foreach( $this->extensions as $extension ) {
				if ( $ready ) {
					if ( $real_class = $this->get_real_class_of_stack_extension( $query, $extension ) ) {
						$this->create_alias_class( $class, $real_class );
						return TRUE;
					}
				} else {
					if ( $query_extension == $extension['namespace'] ) {
						$ready = TRUE;
					}
				}
			}
			$query = \Staq\Util::stack_query_pop( $query );
			$ready = TRUE;
		}

		$this->create_empty_class( $class );
	}



	/*************************************************************************
	  CLASS DECLARATION             
	 *************************************************************************/
	protected function class_exists( $class ) {
		return ( class_exists( $class ) || interface_exists( $class ) );
	}
	protected function create_alias_class( $alias, $class ) {
		return $this->create_class( $alias, $class, interface_exists( $class ) );
	}
	protected function create_empty_class( $class ) {
		return $this->create_class( $class, NULL );
	}
	protected function create_class( $class, $base_class, $is_interface = FALSE ) {
		$namespace = \UObject::get_namespace( $class, '\\' );
		$name = \UObject::get_class_name( $class, '\\' );
		$code = '';
		if ( $namespace ) {
			$code = 'namespace ' . $namespace . ';' . PHP_EOL;
		}
		if ( $is_interface ) {
			$code .= 'interface';
		} else {
			$code .= 'class';
		}
		$code .= ' ' . $name . ' ';
		if ( $base_class ) {
			$code .= 'extends \\' . $base_class . ' ';
		}
		$code .= '{ }' . PHP_EOL;
		// echo $code . HTML_EOL;
		eval( $code );
	}
}
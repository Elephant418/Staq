<?php

/* This file is part of the Staq project, which is under MIT license */

namespace Staq;

abstract class Util {



	/*************************************************************************
	  STRING METHODS                   
	 *************************************************************************/

	// PATH FUNCTIONS
	public static function string_dirname( $path, $level = 1 ) {
		for ( $i = 0; $i < $level; $i++ ) {
			$path = \dirname( $path );
		}
		return $path;
	}

	public static function string_basename( $path, $level = 1 ) {
		$dirpath = \Staq\Util::string_dirname( $path, $level );
		return substr( $path, strlen( $dirpath ) + 1 );
	}

	public static function file_extension( $path ) {
		return \UString::substr_after_last( $path, '.' );
	}

	public static function string_path_to_namespace( $path ) {
		return str_replace( DIRECTORY_SEPARATOR , '\\', $path );
	}

	public static function string_namespace_to_path( $namespace ) {
		return str_replace( '\\', DIRECTORY_SEPARATOR , $namespace );
	}



	/*************************************************************************
	  HTTP METHODS                   
	 *************************************************************************/
	public static function http_redirect( $url ) {
		header( 'HTTP/1.1 302 Moved Temporarily' );
		header( 'Location: ' . $url );
		die( );
	}
	public static function http_action_redirect( $uri ) {
		\Staq\Util::http_redirect( \Staq::App()->get_base_uri( ) . substr( $uri, 1 ) );
	}



	/*************************************************************************
	  STAQ METHODS                   
	 *************************************************************************/

	// STACK QUERY
	public static function stack_query_pop( $string ) {
		if ( \Staq\Util::is_stack_query_popable( $string ) ) {
			$string = \UString::substr_before_last( $string, '\\' );
		} else {
			$string = NULL;
		}
		return $string;
	}
	public static function is_stack_query_popable( $string ) {
		return ( \UString::has( $string, '\\' ) );
	}

	// STACK OBJECT
	public static function is_stack_object( $stack ) {
		return ( is_object( $stack ) && \Staq\Util::is_stack( $stack ) );
	}
	public static function is_stack( $stack, $query = 'Stack\\' ) {
		\UObject::do_convert_to_class( $stack );
		return \UString::is_start_with( $stack, $query );
	}
	public static function stack_query( $stack ) {
		\UObject::do_convert_to_class( $stack );
		if ( \Staq\Util::is_stack( $stack ) ) {
			return substr( $stack, strlen( 'Stack\\' ) );
		}
	}
	public static function stack_sub_query( $stack, $separator = '\\' ) {
		$query = \Staq\Util::stack_query( $stack );
		$sub_query = \UString::substr_after( $query, '\\' );
		return str_replace( '\\', $separator, $sub_query );
	}
	public static function stack_sub_query_text( $stack ) {
		$sub_query = \Staq\Util::stack_sub_query( $stack );
		return str_replace( [ '\\', '_' ], ' ', $sub_query );
	}
	public static function stack_definition( $stack ) {
		if ( \Staq\Util::is_stack( $stack ) ) {
			$parents = [ ];
			while ( $stack = get_parent_class( $stack ) ) {
				if ( 
					\Staq\Util::is_stackable_class( $stack ) && 
					! \Staq\Util::is_parent_stack( $stack )
				) {
					$parents[ ] = $stack;
				}
			}
			return $parents;
		}
	}
	public static function stack_height( $stack ) {
		return count( \Staq\Util::stack_definition( $stack ) ); 
	}
	public static function stack_definition_contains( $stack, $class ) {
		return is_a( $stack, $class ); 
	}
	public static function stack_debug( $stack ) {
		$list = [ ];
		foreach ( \Staq\Util::stack_definition( $stack ) as $key => $stackable ) {
			$debug                = [ ];
			$debug[ 'query'     ] = \Staq\Util::stackable_query( $stackable );
			$debug[ 'extension' ] = \Staq\Util::stackable_extension( $stackable );
			$list[ ] = $debug;
		}
		return $list;
	}
	public static function get_declared_stack_classes( ) {
		$stack_classes = [ ];
		foreach ( get_declared_classes( ) as $class ) {
			if ( \Staq\Util::is_stack( $class ) ) {
				$stack_classes[ ] = $class;
			}
		}
		return $stack_classes;
	}

	// STACKABLE CLASS
	public static function is_stackable_class( $stackable ) {
		\UObject::do_convert_to_class( $stackable );
		return ( \UString::has( $stackable, '\\Stack\\' ) );
	}
	public static function stackable_extension( $stackable ) {
		\UObject::do_convert_to_class( $stackable );
		return ( \UString::substr_before( $stackable, '\\Stack\\' ) );
	}
	public static function stackable_query( $stackable ) {
		\UObject::do_convert_to_class( $stackable );
		return ( \UString::substr_after( $stackable, '\\Stack\\' ) );
	}
	public static function is_parent_stack( $stackable ) {
		\UObject::do_convert_to_class( $stackable );
		return ( \UString::is_end_with( $stackable, '\\__Parent' ) );
	}
	public static function parent_stack_query( $stackable ) {
		\UObject::do_convert_to_class( $stackable );
		$query = \Staq\Util::stackable_query( $stackable );
		return ( \UString::substr_before( $query, '\\__Parent' ) );
	}
}
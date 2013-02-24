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
		return \UString::substrAfterLast( $path, '.' );
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
			$string = \UString::substrBeforeLast( $string, '\\' );
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
		return ( is_object( $stack ) && \Staq\Util::isStack( $stack ) );
	}
	public static function isStack( $stack, $query = 'Stack\\' ) {
		\UObject::doConvertToClass( $stack );
		return \UString::isStartWith( $stack, $query );
	}
	public static function stackQuery( $stack ) {
		\UObject::doConvertToClass( $stack );
		if ( \Staq\Util::isStack( $stack ) ) {
			return substr( $stack, strlen( 'Stack\\' ) );
		}
	}
	public static function stack_sub_query( $stack, $separator = '\\' ) {
		$query = \Staq\Util::stackQuery( $stack );
		$sub_query = \UString::substrAfter( $query, '\\' );
		return str_replace( '\\', $separator, $sub_query );
	}
	public static function stack_sub_query_text( $stack ) {
		$sub_query = \Staq\Util::stack_sub_query( $stack );
		return str_replace( [ '\\', '_' ], ' ', $sub_query );
	}
	public static function stack_definition( $stack ) {
		if ( \Staq\Util::isStack( $stack ) ) {
			$parents = [ ];
			while ( $stack = get_parent_class( $stack ) ) {
				if ( 
					\Staq\Util::is_stackable_class( $stack ) && 
					! \Staq\Util::isParentStack( $stack )
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
			$debug[ 'query'     ] = \Staq\Util::getStackableQuery( $stackable );
			$debug[ 'extension' ] = \Staq\Util::getStackableExtension( $stackable );
			$list[ ] = $debug;
		}
		return $list;
	}
	public static function get_declared_stack_classes( ) {
		$stack_classes = [ ];
		foreach ( get_declared_classes( ) as $class ) {
			if ( \Staq\Util::isStack( $class ) ) {
				$stack_classes[ ] = $class;
			}
		}
		return $stack_classes;
	}

	// STACKABLE CLASS
	public static function is_stackable_class( $stackable ) {
		\UObject::doConvertToClass( $stackable );
		return ( \UString::has( $stackable, '\\Stack\\' ) );
	}
	public static function getStackableExtension( $stackable ) {
		\UObject::doConvertToClass( $stackable );
		return ( \UString::substrBefore( $stackable, '\\Stack\\' ) );
	}
	public static function getStackableQuery( $stackable ) {
		\UObject::doConvertToClass( $stackable );
		return ( \UString::substrAfter( $stackable, '\\Stack\\' ) );
	}
	public static function isParentStack( $stackable ) {
		\UObject::doConvertToClass( $stackable );
		return ( \UString::isEndWith( $stackable, '\\__Parent' ) );
	}
	public static function getParentStackQuery( $stackable ) {
		\UObject::doConvertToClass( $stackable );
		$query = \Staq\Util::getStackableQuery( $stackable );
		return ( \UString::substrBefore( $query, '\\__Parent' ) );
	}
}
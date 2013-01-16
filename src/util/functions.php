<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util;



/*************************************************************************
  STRING METHODS                   
 *************************************************************************/

// PATH FUNCTIONS
function string_dirname( $path, $level = 1 ) {
	for ( $i = 0; $i < $level; $i++ ) {
		$path = \dirname( $path );
	}
	return $path;
}

function string_basename( $path, $level = 1 ) {
	$dirpath = \Staq\Util\string_dirname( $path, $level );
	return substr( $path, strlen( $dirpath ) + 1 );
}

function file_extension( $path ) {
	return \UString::substr_after_last( $path, '.' );
}

function string_path_to_namespace( $path ) {
	$namespace = implode( '\\', array_map( 'ucfirst', explode( '/', $path ) ) );
	return $namespace;
}

function string_namespace_to_path( $namespace ) {
	return strtolower( str_replace( '\\', '/' , $namespace ) );
}

function string_namespace_to_class_path( $namespace ) {
	$parts = explode( '\\', $namespace );
	if ( count( $parts ) == 1 ) {
		return $parts[ 0 ];
	}
	$class = array_pop( $parts );
	return strtolower( implode( '/', $parts ) ) . '/' . $class;
}



/*************************************************************************
  HTTP METHODS                   
 *************************************************************************/
function http_redirect( $url ) {
	header( 'HTTP/1.1 302 Moved Temporarily' );
	header( 'Location: ' . $url );
	die( );
}
function http_action_redirect( $uri ) {
	\Staq\Util\http_redirect( \Staq\Application::get_root_uri( ) . substr( $uri, 1 ) );
}



/*************************************************************************
  STAQ METHODS                   
 *************************************************************************/

// STACK QUERY
function stack_query_pop( $string ) {
	if ( \Staq\Util\is_stack_query_popable( $string ) ) {
		$string = \UString::substr_before_last( $string, '\\' );
	} else {
		$string = NULL;
	}
	return $string;
}
function is_stack_query_popable( $string ) {
	return ( \UString::has( $string, '\\' ) );
}

// STACK OBJECT
function is_stack_object( $stack ) {
	return ( is_object( $stack ) && \Staq\Util\is_stack( $stack ) );
}
function is_stack( $stack ) {
	\UObject::do_convert_to_class( $stack );
	return \UString::is_start_with( $stack, 'Stack\\' );
}
function stack_query( $stack ) {
	\UObject::do_convert_to_class( $stack );
	if ( \Staq\Util\is_stack( $stack ) ) {
		return substr( $stack, strlen( 'Stack\\' ) );
	}
}
function stack_sub_query( $stack ) {
	$query = \Staq\Util\stack_query( $stack );
	return \UString::substr_after( $query, '\\' );
}
function stack_sub_query_text( $stack ) {
	$sub_query = \Staq\Util\stack_sub_query( $stack );
	return str_replace( [ '\\', '_' ], ' ', $sub_query );
}
function stack_definition( $stack ) {
	if ( \Staq\Util\is_stack( $stack ) ) {
		$parents = [ ];
		while ( $stack = get_parent_class( $stack ) ) {
			if ( 
				\Staq\Util\is_stackable_class( $stack ) && 
				! \Staq\Util\is_parent_stack( $stack )
			) {
				$parents[ ] = $stack;
			}
		}
		return $parents;
	}
}
function stack_height( $stack ) {
	return count( stack_definition( $stack ) ); 
}
function stack_definition_contains( $stack, $class ) {
	return is_a( $stack, $class ); 
}
function stack_debug( $stack ) {
	$str = 'Debug of ' . get_class( $stack ) . '<ol>';
	foreach ( \Staq\Util\stack_definition( $stack ) as $key => $stackable ) {
		$str .= '<li>' . stackable_query( $stackable ) . ' from extension ' . stackable_extension( $stackable ) . '</li>';
	}
	$str .= '</ol>';
	echo $str;
}

// STACKABLE CLASS
function is_stackable_class( $stackable ) {
	\UObject::do_convert_to_class( $stackable );
	return ( \UString::has( $stackable, '\\Stack\\' ) );
}
function stackable_extension( $stackable ) {
	\UObject::do_convert_to_class( $stackable );
	return ( \UString::substr_before( $stackable, '\\Stack\\' ) );
}
function stackable_query( $stackable ) {
	\UObject::do_convert_to_class( $stackable );
	return ( \UString::substr_after( $stackable, '\\Stack\\' ) );
}
function is_parent_stack( $stackable ) {
	\UObject::do_convert_to_class( $stackable );
	return ( \UString::is_end_with( $stackable, '\\__Parent' ) );
}
function parent_stack_query( $stackable ) {
	\UObject::do_convert_to_class( $stackable );
	$query = \Staq\Util\stackable_query( $stackable );
	return ( \UString::substr_before( $query, '\\__Parent' ) );
}
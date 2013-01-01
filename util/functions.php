<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Util;


/*************************************************************************
  STRING METHODS                   
 *************************************************************************/


// STARTS WITH & ENDS WITH FUNCTIONS
function string_starts_with( $hay, $needles ) {
	must_be_array( $needles );
	foreach( $needles as $needle ) {
		if ( substr( $hay, 0, strlen( $needle ) ) == $needle ) {
			return TRUE;
		}
	}
	return FALSE;
}

function string_ends_with( $hay, $needles ) {
	must_be_array( $needles );
	foreach( $needles as $needle ) {
		if ( substr( $hay, -strlen( $needle ) ) == $needle ) {
			return TRUE;
		}
	}
	return FALSE;
}

function string_i_starts_with( $hay, $needle ) {
	return starts_with( strtolower( $hay ), strtolower( $needle ) );
}

function string_i_ends_with( $hay, $needle ) {
	return ends_with( strtolower( $hay ), strtolower( $needle ) );
}

function string_must_starts_with( &$hay, $needle ) {
	if ( ! starts_with( $hay, $needle ) ) {
		$hay = $needle . $hay;
	}
}

function string_must_ends_with( &$hay, $needle ) {
	if ( ! ends_with( $hay, $needle ) ) {
		$hay .= $needle;
	}
}

function string_must_not_starts_with( &$hay, $needle ) {
	if ( starts_with( $hay, $needle ) ) {
		$hay = substr( $hay, strlen( $needle ) );
	}
}

function string_must_not_ends_with( &$hay, $needle ) {
	if ( ends_with( $hay, $needle ) ) {
		$hay = substr( $hay, 0, -strlen( $needle ) );
	}
}



// CONTAINS FUNCTIONS
function string_contains( $hay, $needle ) {
	return ( strpos( $hay, $needle ) !== false );
}

function string_i_contains( $hay, $needle ) {
	return string_contains( strtolower( $hay ), strtolower( $needle ) );
}



// SUBSTRING FUNCTIONS
function string_cut_before( &$hay, $needles ) {
	$return = substr_before( $hay, $needles );
	$hay = substr( $hay, strlen( $return ) );
	return $return;
}

function string_substr_before( $hay, $needles ) {
	must_be_array( $needles );
	$return = $hay;
	foreach( $needles as $needle ) {
		if ( ! empty( $needle) && string_contains( $hay, $needle ) ) {
			$cut = substr( $hay, 0, strpos( $hay, $needle ) );
			if ( strlen( $cut ) < strlen ( $return ) ) {
				$return = $cut;
			}
		}
	}
	$hay = substr( $hay, strlen( $return ) );
	return $return;
}

function string_cut_before_last( &$hay, $needles ) {
	$return = substr_before_last( $hay, $needles );
	$hay = substr( $hay, strlen( $return ) );
	return $return;
}

function string_substr_before_last( $hay, $needles ) {
	must_be_array( $needles );
	$return = '';
	foreach( $needles as $needle ) {
		if ( ! empty( $needle) && string_contains( $hay, $needle ) ) {
			$cut = substr( $hay, 0, strrpos( $hay, $needle ) );
			if ( strlen( $cut ) > strlen ( $return ) ) {
				$return = $cut;
			}
		}
	}
	$hay = substr( $hay, strlen( $return ) );
	return $return;
}

function string_cut_after( &$hay, $needles ) {
	$return = substr_after( $hay, $needles );
	$hay = substr( $hay, 0, - strlen( $return ) );
	return $return;
}

function string_substr_after( $hay, $needles ) {
	must_be_array( $needles );
	$return = '';
	foreach( $needles as $needle ) {
		if ( ! empty( $needle) && string_contains( $hay, $needle ) ) {
			$cut = substr( $hay, strpos( $hay, $needle ) + strlen( $needle ) );
			if ( strlen( $cut ) > strlen ( $return ) ) {
				$return = $cut;
			}
		}
	}
	return $return;
}

function string_cut_after_last( &$hay, $needles ) {
	$return = substr_after_last( $hay, $needles );
	$hay = substr( $hay, 0, - strlen( $return ) );
	return $return;
}

function string_substr_after_last( $hay, $needles ) {
	must_be_array( $needles );
	$return = $hay;
	foreach( $needles as $needle ) {
		if ( ! empty( $needle) && string_contains( $hay, $needle ) ) {
			$cut = substr( $hay, strrpos( $hay, $needle ) + strlen( $needle ) );
			if ( strlen( $cut ) < strlen ( $return ) ) {
				$return = $cut;
			}
		}
	}
	return $return;
}



// RANDOM FUNCTIONS
function string_random( $length = 10, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ) {
	$string = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$string .= $characters[ mt_rand( 0, strlen( $characters ) - 1 ) ];
	}
	return $string;
}



// PATH FUNCTIONS
function string_dirname( $path, $level = 1 ) {
	for ( $i = 0; $i < $level; $i++ ) {
		$path = \dirname( $path );
	}
	return $path;
}

function string_basename( $path, $level = 1 ) {
	$dirpath = string_dirname( $path, $level );
	return substr( $path, strlen( $dirpath ) + 1 );
}

function file_extension( $path ) {
	return substr_after_last( $path, '.' );
}



/*************************************************************************
  ARRAY METHODS                   
 *************************************************************************/
function must_be_array( &$array ) {
	if ( ! is_array( $array ) ) {
		$array = [ $array ];
	}
}

// Keep the order of each FIRST occurence 
function array_merge_unique( $array1, $array2 ) {
	return array_values( array_unique( array_merge( $array1, $array2 ) ) );
}

// Keep the order of each LAST occurence 
function array_reverse_merge_unique( $array1, $array2 ) {
	return array_reverse( array_values( array_unique( array_reverse( array_merge( $array1, $array2 ) ) ) ) );
}


/*************************************************************************
  STAQ METHODS                   
 *************************************************************************/

// STACK CLASS
function is_stack_class( $class ) {
	return ( \Staq\Util\string_starts_with( $class, 'Stack\\' ) );
}
function is_parent_stack_class( $class ) {
	return ( \Staq\Util\string_ends_with( $class, '\\__Parent' ) );
}

// STACK NAME
function stack_query_pop( $string ) {
	if ( \Staq\Util\is_stack_query_popable( $string ) ) {
		$string = \Staq\Util\string_substr_before_last( $string, '\\' );
		if ( ! \Staq\Util\string_contains( $string, '\\' ) ) {
			$string = $string . '\\' . \Staq\Autoloader::DEFAULT_CLASS;
		}
	} else {
		$string = NULL;
	}
	return $string;
}
function is_stack_query_popable( $string ) {
	return ( \Staq\Util\string_contains( $string, '\\' ) && ! \Staq\Util\is_default_stack_query( $string ) );
}
function is_default_stack_query( $string ) {
	return \Staq\Util\string_ends_with( $string, '\\' . \Staq\Autoloader::DEFAULT_CLASS );
}

// OBJECT
function is_stack( $stack ) {
	if ( is_object( $stack ) ) {
		$stack = get_class( $stack );
	}
	return \Staq\Util\string_starts_with( $stack, 'Stack\\');
}
function stack_query( $stack ) {
	if ( \Staq\Util\is_stack( $stack ) ) {
		return substr( get_class( $stack ), strlen( 'Stack\\' ) );
	}
}
function stack_definition( $stack ) {
	if ( \Staq\Util\is_stack( $stack ) ) {
		$parents = [ ];
		while ( $stack = get_parent_class( $stack ) ) {
			if ( ! \Staq\Util\is_parent_stack_class( $stack ) ) {
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
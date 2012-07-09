<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq;


/*************************************************************************
  STRING METHODS                   
 *************************************************************************/
function starts_with( $hay, $needles ) {
	if ( ! is_array( $needles ) ) {
		$needles = array( $needles );
	}
	foreach( $needles as $needle ) {
		if ( substr( $hay, 0, strlen( $needle ) ) == $needle ) {
			return TRUE;
		}
	}
	return FALSE;
}
function ends_with( $hay, $needles ) {
	if ( ! is_array( $needles ) ) {
		$needles = array( $needles );
	}
	foreach( $needles as $needle ) {
		if ( substr( $hay, -strlen( $needle ) ) == $needle ) {
			return TRUE;
		}
	}
	return FALSE;
}
function i_starts_with( $hay, $needle ) {
	return starts_with( strtolower( $hay ), strtolower( $needle ) );
}
function i_ends_with( $hay, $needle ) {
	return ends_with( strtolower( $hay ), strtolower( $needle ) );
}
function contains( $hay, $needle ) {
	return ( strpos( $hay, $needle ) !== false );
}
function i_contains( $hay, $needle ) {
	return contains( strtolower( $hay ), strtolower( $needle ) );
}
function substr_before( $hay, $needle ) {
	if ( contains( $hay, $needle ) ) {
		return substr( $hay, 0, strpos( $hay, $needle ) );
	}
	return $hay;
}
function substr_before_last( $hay, $needle ) {
	if ( contains( $hay, $needle ) ) {
		return substr( $hay, 0, strrpos( $hay, $needle ) );
	}
	return $hay;
}
function substr_after( $hay, $needle ) {
	if ( contains( $hay, $needle ) ) {
		return substr( $hay, strpos( $hay, $needle ) + strlen( $needle ) );
	}
	return $hay;
}
function substr_after_last( $hay, $needle ) {
	if ( contains( $hay, $needle ) ) {
		return substr( $hay, strrpos( $hay, $needle ) + strlen( $needle ) );
	}
	return $hay;
}
function must_starts_with( $hay, $needle ) {
	if ( ! starts_with( $hay, $needle ) ) {
		$hay = $needle . $hay;
	}
	return $hay;
}
function must_ends_with( $hay, $needle ) {
	if ( ! ends_with( $hay, $needle ) ) {
		$hay .= $needle;
	}
	return $hay;
}
function must_not_starts_with( $hay, $needle ) {
	if ( starts_with( $hay, $needle ) ) {
		$hay = substr( $hay, strlen( $needle ) );
	}
	return $hay;
}
function must_not_ends_with( $hay, $needle ) {
	if ( ends_with( $hay, $needle ) ) {
		$hay = substr( $hay, 0, -strlen( $needle ) );
	}
	return $hay;
}
function random( $length = 10 ) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$string = '';    
	for ( $i = 0; $i < $length; $i++ ) {
		$string .= $characters[ mt_rand( 0, strlen( $characters ) - 1 ) ];
	}
	return $string;
}

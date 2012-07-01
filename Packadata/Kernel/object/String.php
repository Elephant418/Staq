<?php

namespace Supersoniq\Packadata\Kernel\Object;

abstract class String {


	/*************************************************************************
	  STATIC METHODS                   
	 *************************************************************************/
	static function starts_with( $hay, $needle ) {
		return substr( $hay, 0, strlen( $needle ) ) == $needle;
	}
	static function ends_with( $hay, $needle ) {
		return substr( $hay, -strlen( $needle ) ) == $needle;
	}
	static function i_starts_with( $hay, $needle ) {
		return String::starts_with( strtolower( $hay ), strtolower( $needle ) );
	}
	static function i_ends_with( $hay, $needle ) {
		return String::ends_with( strtolower( $hay ), strtolower( $needle ) );
	}
	static function contains( $hay, $needle ) {
		return ( strpos( $hay, $needle ) !== false );
	}
	static function i_contains( $hay, $needle ) {
		return String::contains( strtolower( $hay ), strtolower( $needle ) );
	}
	static function substr_before( $hay, $needle ) {
		if ( String::contains( $hay, $needle ) ) {
			return substr( $hay, 0, strpos( $hay, $needle ) );
		}
		return $hay;
	}
	static function substr_before_last( $hay, $needle ) {
		if ( String::contains( $hay, $needle ) ) {
			return substr( $hay, 0, strrpos( $hay, $needle ) );
		}
		return $hay;
	}
	static function substr_after( $hay, $needle ) {
		if ( String::contains( $hay, $needle ) ) {
			return substr( $hay, strpos( $hay, $needle ) + 1 );
		}
		return $hay;
	}
	static function substr_after_last( $hay, $needle ) {
		if ( String::contains( $hay, $needle ) ) {
			return substr( $hay, strrpos( $hay, $needle ) + 1 );
		}
		return $hay;
	}
	static function must_starts_with( $hay, $needle ) {
		if ( ! String::starts_with( $hay, $needle ) ) {
			$hay = $needle . $hay;
		}
		return $hay;
	}
	static function must_ends_with( $hay, $needle ) {
		if ( ! String::ends_with( $hay, $needle ) ) {
			$hay .= $needle;
		}
		return $hay;
	}
	static function must_not_starts_with( $hay, $needle ) {
		if ( String::starts_with( $hay, $needle ) ) {
			$hay = substr( $hay, 1 );
		}
		return $hay;
	}
	static function must_not_ends_with( $hay, $needle ) {
		if ( String::ends_with( $hay, $needle ) ) {
			$hay = substr( $hay, 0, -1 );
		}
		return $hay;
	}
	static function random( $length = 10 ) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$string = '';    
		for ( $i = 0; $i < $length; $i++ ) {
			$string .= $characters[ mt_rand( 0, strlen( $characters ) - 1 ) ];
		}
		return $string;
	}
}

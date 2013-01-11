<?php

/* This file is part of the Ubiq project, which is under MIT license */

namespace Ubiq {
	const VERSION = '0.1';
}


/*************************************************************************
  STRING METHODS                   
 *************************************************************************/
namespace UString {



	// STARTS WITH & ENDS WITH FUNCTIONS
	function starts_with( $hay, $needles ) {
		\UArray\must_be_array( $needles );
		foreach( $needles as $needle ) {
			if ( substr( $hay, 0, strlen( $needle ) ) == $needle ) {
				return TRUE;
			}
		}
		return FALSE;
	}

	function i_starts_with( $hay, $needles ) {
		\UArray\must_be_array( $needles );
		$needles = array_map( 'strtolower', $needles );
		return \UString\starts_with( strtolower( $hay ), $needles );
	}

	function must_starts_with( &$hay, $needle ) {
		if ( ! \UString\starts_with( $hay, $needle ) ) {
			$hay = $needle . $hay;
		}
	}

	function must_not_starts_with( &$hay, $needle ) {
		if ( \UString\starts_with( $hay, $needle ) ) {
			$hay = substr( $hay, strlen( $needle ) );
		}
	}

	function ends_with( $hay, $needles ) {
		\UArray\must_be_array( $needles );
		foreach( $needles as $needle ) {
			if ( substr( $hay, -strlen( $needle ) ) == $needle ) {
				return TRUE;
			}
		}
		return FALSE;
	}

	function i_ends_with( $hay, $needles ) {
		\UArray\must_be_array( $needles );
		$needles = array_map( 'strtolower', $needles );
		return \UString\ends_with( strtolower( $hay ), $needles );
	}

	function must_ends_with( &$hay, $needle ) {
		if ( ! \UString\ends_with( $hay, $needle ) ) {
			$hay .= $needle;
		}
	}

	function must_not_ends_with( &$hay, $needle ) {
		if ( \UString\ends_with( $hay, $needle ) ) {
			$hay = substr( $hay, 0, -strlen( $needle ) );
		}
	}



	// CONTAINS FUNCTIONS
	function contains( $hay, $needles ) {
		\UArray\must_be_array( $needles );
		foreach( $needles as $needle ) {
			if ( strpos( $hay, $needle ) !== FALSE ) {
				return TRUE;
			}
		}
		return FALSE;
	}

	function i_contains( $hay, $needles ) {
		\UArray\must_be_array( $needles );
		$needles = array_map( 'strtolower', $needles );
		return \UString\contains( $hay, $needles );
	}



	// SUBSTRING FUNCTIONS
	function cut_before( &$hay, $needles ) {
		$return = \UString\substr_before( $hay, $needles );
		$hay = substr( $hay, strlen( $return ) );
		return $return;
	}

	function substr_before( $hay, $needles ) {
		\UArray\must_be_array( $needles );
		$return = $hay;
		foreach( $needles as $needle ) {
			if ( ! empty( $needle) && \UString\contains( $hay, $needle ) ) {
				$cut = substr( $hay, 0, strpos( $hay, $needle ) );
				if ( strlen( $cut ) < strlen ( $return ) ) {
					$return = $cut;
				}
			}
		}
		$hay = substr( $hay, strlen( $return ) );
		return $return;
	}

	function cut_before_last( &$hay, $needles ) {
		$return = \UString\substr_before_last( $hay, $needles );
		$hay = substr( $hay, strlen( $return ) );
		return $return;
	}

	function substr_before_last( $hay, $needles ) {
		\UArray\must_be_array( $needles );
		$return = '';
		foreach( $needles as $needle ) {
			if ( ! empty( $needle ) && \UString\contains( $hay, $needle ) ) {
				$cut = substr( $hay, 0, strrpos( $hay, $needle ) );
				if ( strlen( $cut ) > strlen ( $return ) ) {
					$return = $cut;
				}
			}
		}
		$hay = substr( $hay, strlen( $return ) );
		return $return;
	}

	function cut_after( &$hay, $needles ) {
		$return = \UString\substr_after( $hay, $needles );
		$hay = substr( $hay, 0, strlen( $hay ) - strlen( $return ) );
		return $return;
	}

	function substr_after( $hay, $needles ) {
		\UArray\must_be_array( $needles );
		$return = '';
		foreach( $needles as $needle ) {
			if ( ! empty( $needle) && \UString\contains( $hay, $needle ) ) {
				$cut = substr( $hay, strpos( $hay, $needle ) + strlen( $needle ) );
				if ( strlen( $cut ) > strlen ( $return ) ) {
					$return = $cut;
				}
			}
		}
		return $return;
	}

	function cut_after_last( &$hay, $needles ) {
		$return = \UString\substr_after_last( $hay, $needles );
		$hay = substr( $hay, 0, strlen( $hay ) - strlen( $return ) );
		return $return;
	}

	function substr_after_last( $hay, $needles ) {
		\UArray\must_be_array( $needles );
		$return = $hay;
		foreach( $needles as $needle ) {
			if ( ! empty( $needle) && \UString\contains( $hay, $needle ) ) {
				$cut = substr( $hay, strrpos( $hay, $needle ) + strlen( $needle ) );
				if ( strlen( $cut ) < strlen ( $return ) ) {
					$return = $cut;
				}
			}
		}
		return $return;
	}



	// RANDOM FUNCTIONS
	function random( $length = 10, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ) {
		$string = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$string .= $characters[ mt_rand( 0, strlen( $characters ) - 1 ) ];
		}
		return $string;
	}



	// SPECIAL CHARACTERS
	function strip_accent( $string ) {
		$match   = [ 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ' ];
		$replace = [ 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o' ];
		return str_replace( $match, $replace, $string );
	}

	function strip_special_char( $string, $characters = '-_a-zA-Z0-9', $replace = '-' ) {
		$string = preg_replace( '/[^' . $characters . ']/s', $replace, $string );
		if ( ! empty( $replace ) ) {
			$string = preg_replace( '/[' . $replace . ']+/s', $replace, $string );
		}
		return $string;
	}
}



/*************************************************************************
  ARRAY METHODS                   
 *************************************************************************/
namespace UArray {

	function must_be_array( &$array ) {
		if ( ! is_array( $array ) ) {
			$array = [ $array ];
		}
	}

	function must_valid_schema( &$array, $schema ) {
		foreach ( $schema as $key => $value ) {
			if ( is_numeric( $key ) ) {
				if ( ! isset( $array[ $value ] ) ) {
					return FALSE;
				}
			} else {
				if ( ! isset( $array[ $key ] ) ) {
					$array[ $key ] = $value;
				}
			}
		}
		return TRUE;
	}

	// Keep the order of each FIRST occurence 
	function merge_unique( $array1, $array2 ) {
		return array_values( array_unique( call_user_func_array( 'array_merge', func_get_args( ) ) ) );
	}

	// Keep the order of each LAST occurence 
	function reverse_merge_unique( $array1, $array2 ) {
		return array_reverse( array_values( array_unique( array_reverse( call_user_func_array( 'array_merge', func_get_args( ) ) ) ) ) );
	}
}



/*************************************************************************
  OBJECT METHODS                   
 *************************************************************************/
namespace UObject {

	function must_be_class( &$class ) {
		if ( is_object( $class ) ) {
			$class = get_class( $class );
		} else {
			\UString\must_not_starts_with( $class, '\\' );
		}
	}

	function get_attribute_names( $class ) {
		\UObject\must_be_class( $class );
		if ( class_exists( $class ) ) {
			return array_keys( get_class_vars( $class ) );
		}
		return FALSE;
	}

	function get_namespace( $class ) {
		\UObject\must_be_class( $class );
		return \UString\substr_before_last( $class, '\\' );
	}

	function get_class_name( $class ) {
		\UObject\must_be_class( $class );
		return \UString\substr_after_last( $class, '\\' );
	}
}
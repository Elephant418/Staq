<?php

/* Todo MIT license
 */

namespace Staq;

abstract class Autoloader {

	public static function autoload( $original_class ) {
		// Stack
		if ( \Staq\Util\string_starts_with( $original_class, 'Stack\\' ) ) {
			$stack = \Staq\Util\string_substr_after( $original_class, 'Stack\\' );
			// Defined class
			while( \Staq\Util\string_contains( $stack, '\\' ) ) {
				foreach( Application::$extensions as $extension ) {
					$relative_path = self::string_namespace_to_path( $stack );
					$absolute_path = STAQ_ROOT_PATH . $extension . '/' . $relative_path . '.php';
					if ( is_file( $absolute_path ) ) {
						include_once( $absolute_path );
						$namespace = self::string_path_to_namespace( $extension . '/' . $relative_path );
						self::create_class( $class, $namespace );
						return TRUE;
					}
				}
				$stack = \Staq\Util\string_substr_before_last( $stack, '\\' );
			}
			// Anonymous class

			// Empty class
			self::create_class( $original_class );
		}
		// __Parent
	}
	

	public static function create_class( $class, $base_class = NULL ) {
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
		echo $code . HTML_EOL;
		eval( $code );
	}

	public static function string_path_to_namespace( $path, $absolute = TRUE ) {
		$namespace = implode( '\\', array_map( function( $a ) {
			return ucfirst( $a );
		}, explode( '/', $path ) ) );
		if ( $absolute ) $namespace = '\\' . $namespace;
		return $namespace;
	}

	public static function string_namespace_to_path( $namespace, $file = TRUE ) {
		if ( $file ) {
			$parts = explode( '\\', $namespace );
			$class = array_pop( $parts );
			return strtolower( implode( '/', $parts ) ) . '/' . $class;
		}
		return str_replace( '\\', '/' , $namespace );
	}
}
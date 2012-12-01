<?php

/* Todo MIT license
 */

namespace Staq;

abstract class Autoloader {

	public static function autoload( $class ) {
		// Start by Stack
		if ( \Staq\util\string_starts_with( $class, 'Stack\\' ) ) {
			echo \Staq\util\string_substr_after( $class, 'Stack\\' );
			// foreach parts + __Anonyme
				// foreach extensions
					// Est-ce que le file existe
					// Créer un alias
					// return
				//
			// encore là ?
			// créer l'alias d'une class vide
		}
		// End by __Parent
	}
	

	public static function create_class( $base_class, $class ) {
		$namespace = \Supersoniq\substr_before_last( $class, '\\' );
		$name = \Supersoniq\substr_after_last( $class, '\\' );
		$code = '';
		if ( $namespace ) {
			$code = 'namespace ' . $namespace . ';' . PHP_EOL;
		}
		$code .= 'class ' . $name . ' extends ' . $base_class . ' { }' . PHP_EOL;
		// echo $code . HTML_EOL;
		eval( $code );
	}
}
<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq;

class Autoloader {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $extensions = [ ];
	static public $initialized = FALSE;
	static public $cache_file;



	/*************************************************************************
	  INITIALIZATION             
	 *************************************************************************/
	public function __construct( $extensions ) {
		$this->extensions = $extensions;
	}
	
	protected function initialize( ) {
		if ( \Staq::App( ) && \Staq::App( )->isInitialized( ) ) {
			static::$cache_file = reset( $this->extensions ) . '/cache/autoload.php';
			if ( is_file( static::$cache_file ) ) {
				require_once( static::$cache_file );
			}
			static::$initialized = TRUE;
		}
		return $this;
	}



	/*************************************************************************
	  TOP-LEVEL AUTOLOAD
	 *************************************************************************/
	public function autoload( $class ) {
		if ( ! static::$initialized ) {
			$this->initialize( );
			if ( $this->classExists( $class ) ) {
				return TRUE;
			}
		}
		if ( \Staq\Util::isStack( $class ) ) {
			$this->loadStackClass( $class );
		} else if ( \Staq\Util::isParentStack( $class ) ) {
			$this->loadStackParentClass( $class );
		}
	}


	/*************************************************************************
	  FILE CLASS MANAGEMENT             
	 *************************************************************************/
	protected function loadStackClass( $class ) {
		$stack_query = \Staq\Util::stackQuery( $class );
		while( $stack_query ) {
			foreach( array_keys( $this->extensions ) as $extension_namespace ) {
				if ( $real_class = $this->getRealClass( $stack_query, $extension_namespace ) ) {
					$this->create_alias_class( $class, $real_class );
					return TRUE;
				}
			}
			$stack_query = \Staq\Util::stack_query_pop( $stack_query );
		}

		$this->create_empty_class( $class );
	}

	// "stack" is now a part of the namespace, there is no burgers left at my bakery 
	protected function getRealClass( $stack, $extension_namespace ) {
		$stack_path = \Staq\Util::string_namespace_to_path( $stack );
		$absolute_path = realpath( $this->extensions[ $extension_namespace ] . '/Stack/' . $stack_path . '.php' );
		if ( is_file( $absolute_path ) ) {
			$real_class = $extension_namespace . '\\Stack\\' . $stack;
			return $real_class;
		}
	}
	

	protected function loadStackParentClass( $class ) {
		$query_extension = \Staq\Util::getStackableExtension( $class );
		$query = \Staq\Util::getParentStackQuery( $class );
		$ready = FALSE;
		while( $query ) {
			foreach( array_keys( $this->extensions ) as $extension_namespace ) {
				if ( $ready ) {
					if ( $real_class = $this->getRealClass( $query, $extension_namespace ) ) {
						$this->create_alias_class( $class, $real_class );
						return TRUE;
					}
				} else {
					if ( $query_extension === $extension_namespace ) {
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
	protected function classExists( $class ) {
		return ( \class_exists( $class ) || \interface_exists( $class ) );
	}
	protected function create_alias_class( $alias, $class ) {
		return $this->create_class( $alias, $class, \interface_exists( $class ) );
	}
	protected function create_empty_class( $class ) {
		return $this->create_class( $class, NULL );
	}
	protected function create_class( $class, $base_class, $is_interface = FALSE ) {
		$namespace = \UObject::getNamespace( $class, '\\' );
		$name = \UObject::getClassName( $class, '\\' );
		$code = '';
		if ( $namespace ) {
			$code = 'namespace ' . $namespace . ' {' . PHP_EOL;
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
		$code .= '{ }' . PHP_EOL . '}' . PHP_EOL;
		$this->add_cache( $code );
		eval( $code );
	}

	protected function add_cache( $code ) {
		if ( 
			! static::$initialized ||
			! static::$cache_file || 
			! ( new \Stack\Setting )
				->parse( 'Application' )
				->get_as_boolean( 'cache.autoload' ) ||
			! $handle = @fopen( static::$cache_file, 'a' )
		) {
			return NULL;
		}
		fwrite( $handle, '<?php ' . $code . ' ?>' . PHP_EOL);	
		fclose($handle);		
	}
}
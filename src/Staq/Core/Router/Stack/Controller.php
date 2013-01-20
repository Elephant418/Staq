<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack;

class Controller implements \Stack\IController {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $routes = [ ];
	public static $setting = [ ];



	/*************************************************************************
	  GETTER
	 *************************************************************************/
	public function get_routes( ) {
		return $this->routes;
	}



	/*************************************************************************
	  CONSTRUCTOR             
	 *************************************************************************/
	public function __construct( ) {
		$this->initialize_routes( );
	}



	/*************************************************************************
	  PROTECTED METHODS             
	 *************************************************************************/
	protected function initialize_routes( ) {
		$setting = ( new \Stack\Setting )->parse( $this );
		foreach ( $setting->get_as_array( 'route' ) as $action => $param ) {
			$this->routes[ ] = $this->get_route_from_attribute( $action, $param );
		}
	}
	protected function get_route_from_attribute( $action, $params ) {
		$callable = [ $this, $action ];
		if ( is_callable( $callable ) ) {
			$uri = NULL;
			$exceptions = [ ];
			$aliases = [ ];
			if ( is_array( $params ) ) {
				$uri        = isset( $params[ 'uri'        ] ) ? $params[ 'uri'        ] : $uri;
				$exceptions = isset( $params[ 'exceptions' ] ) ? $params[ 'exceptions' ] : $exceptions;
				$aliases    = isset( $params[ 'aliases'    ] ) ? $params[ 'aliases'    ] : $aliases;
			} else if ( is_string( $params ) ) {
				$uri = $params;
			}
			return new \Stack\Route( $callable, $uri, $exceptions, $aliases );
		}
	}
}

?>
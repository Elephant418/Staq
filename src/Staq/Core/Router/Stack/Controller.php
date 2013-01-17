<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack;

class Controller implements \Stack\IController {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $routes = [ ];



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
	  ACTION METHODS           
	 *************************************************************************/
	public function action( $parameters, $action ) {
	}



	/*************************************************************************
	  PROTECTED METHODS             
	 *************************************************************************/
	protected function initialize_routes( ) {
		foreach( array_keys( get_object_vars( $this ) ) as $attribute_name ) {
			if ( \UString::is_start_with( $attribute_name, 'route_action' ) ) {
				$action = \UString::substr_after( $attribute_name, 'route_' );
				$this->routes[ ] = $this->get_route_from_attribute( $action, $this->$attribute_name );
			}
		}
	}
	protected function get_route_from_attribute( $action, $params ) {
		$callable = [ $this, $action ];
		if ( is_callable( $callable ) ) {
			$match_uri = NULL;
			$match_exceptions = [ ];
			$aliases_uri = [ ];
			if ( is_array( $params ) ) {
				$match_uri        = isset( $params[ 'match_uri'        ] ) ? $params[ 'match_uri'        ] : $match_uri;
				$match_exceptions = isset( $params[ 'match_exceptions' ] ) ? $params[ 'match_exceptions' ] : $match_exceptions;
				$aliases_uri      = isset( $params[ 'aliases_uri'      ] ) ? $params[ 'aliases_uri'      ] : $aliases_uri;
			} else if ( is_string( $params ) ) {
				$match_uri = $params;
			}
			return new \Stack\Route( $callable, $match_uri, $match_exceptions, $aliases_uri );
		}
	}
}

?>
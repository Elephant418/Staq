<?php

/* This file is part of the Staq project, which is under MIT license */


namespace Staq\Core\Router\Stack ;

class Route {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	protected $controller;
	protected $action;
	protected $match_uri;
	protected $match_exception;
	protected $parameters = [ ];
	protected $aliases    = [ ];



	/*************************************************************************
	  CONSTRUCTOR            
	 *************************************************************************/
	public function __construct( $controller, $action, $match_uri, $match_exception = NULL , $aliases = [ ] ) {
		$this->controller      = $controller;
		$this->action          = $action;
		$this->match_uri       = $match_uri;
		$this->match_exception = $match_exception;
		$this->aliases         = $aliases;
	}



	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function call_action( ) {
		$action_method = 'action';
		if ( ! empty( $action ) ) {
			$action_method .= '_' . str_replace( '/', '_', $this->action );
		}
		return call_user_func( [ $this->controller, $action_method ], $this->parameters );
	}
	public function match_uri( $uri ) {
		$pattern = str_replace( [ '.', '+', '?' ],  [ '\.', '\+', '\?' ], $this->match_uri ); 
		$pattern = preg_replace( '#\(([^)]*)\)#', '(?:\1)?', $pattern ); 
		$pattern = preg_replace( '#\:(\w+)#', '(?<\1>\w+)', $pattern ); 
		$pattern = '#^' . $pattern . '/?$#';
		$parameters = [ ];
		$result = preg_match( $pattern, $uri, $parameters );
		if ( $result ) {
			foreach ( array_keys( $matches ) as $key ) {
				if ( is_numeric( $key ) ) {
					unset( $parameters[ $key ] );
				}
			}
		} else {
			$parameters = [ ];
		}
		$this->parameters = $parameters;
		return $result;
	}
	public function match_exception( $exception ) {
		return FALSE;
	}



	/*************************************************************************
	  PRIVATE METHODS             
	 *************************************************************************/
}
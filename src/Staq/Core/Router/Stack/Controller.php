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
	  ACTION METHODS           
	 *************************************************************************/
	public function action_view( ) {
		$page = new \Stack\View;
		$page[ 'template' ] = $this->get_sub_template( );
		return $page;
	}



	/*************************************************************************
	  PRIVATE METHODS           
	 *************************************************************************/
	protected function get_sub_template( ) {
		return strtolower( \Staq\Util::stack_sub_query( $this, '/' ) ) . '.html';
	}
	
	protected function initialize_routes( ) {
		$setting = ( new \Stack\Setting )->parse( $this );
		foreach ( $setting->get_as_array( 'route' ) as $action => $setting ) {
			$route_name = \Stack\Router::get_route_name( $this, $action );
			$this->routes[ $route_name ] = ( new \Stack\Route )->by_setting( $this, $action, $setting );
		}
	}
}

?>
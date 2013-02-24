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
		return ( new \Stack\View )->by_name( \Staq\Util::stack_sub_query( $this ) );
	}



	/*************************************************************************
	  PRIVATE METHODS           
	 *************************************************************************/
	protected function initialize_routes( ) {
		$setting = ( new \Stack\Setting )->parse( $this );
		foreach ( $setting->getAsArray( 'route' ) as $action => $setting ) {
			$this->routes[ $action ] = ( new \Stack\Route )->by_setting( $this, $action, $setting );
		}
	}
}

?>
<?php

/* Todo MIT license
 */

namespace Staq\core;

class Application {



	/*************************************************************************
	 ATTRIBUTES
	 *************************************************************************/
	static public $instance; // Singleton
	private $name;
	private $pltaform;
	private $extensions = [ ];
	private $controllers = [ ];



	/*************************************************************************
	  GETTER             
	 *************************************************************************/
	public function get_name( ) {
		return $this->name;
	}
	public function get_extensions( ) {
		return $this->extensions;
	}
	public function get_platform( ) {
		return $this->platform;
	}



	/*************************************************************************
	  INITIALIZATION             
	 *************************************************************************/
	public function __construct( $name = 'anonymous', $platform = 'prod' ) {
		$this->name = $name;
		$this->platform = $platform;
		// parse settings to determine extensions
		self::$instance = $this;
	}



	/*************************************************************************
	  PUBLIC METHODS             
	 *************************************************************************/
	public function start( ) {
	}
	public function add_controller( $controller ) {
		$this->controllers[ ] = $controller;
	}
	public function run( ) {
		$this->start( );
		// regarder si un controller d'application répond à l'url
		// regarder si des controllers d'extension répond à l'url
		// catcher les exceptions
		// Lever une erreur 404
	}

}

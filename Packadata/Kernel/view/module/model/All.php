<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\View\Module\Model;

abstract class All extends All\__Parent {



	/*************************************************************************
	  ATTRIBUTES                   
	 *************************************************************************/
	public $pagination_size = 25;
	public $pagination_max_page_displayed = 7;



	/*************************************************************************
	  RENDER METHODS                   
	 *************************************************************************/
	public function fill( $template, $parameters = [ ] ) {

		// List
		$models = [ ];
		if ( 
			isset( $_GET[ 'from' ][ 'type' ] ) &&
			isset( $_GET[ 'from' ][ 'id'   ] ) &&
			isset( $_GET[ 'from' ][ 'attribute' ] ) 
		) {
			$template->from = ( new \Model )
				->by_type( $_GET[ 'from' ][ 'type' ] )
				->by_id( $_GET[ 'from' ][ 'id'   ] );
			$models = $template->from->get( $_GET[ 'from' ][ 'attribute' ] );
		} else {
			$models = $this->get_controller( )->all( );
		}
		$template->filter = '';
		if ( isset( $_GET[ 'filter' ] ) ) {
			\Supersoniq\must_be_array( $_GET[ 'filter' ] );
			$template->filter = implode( ', ', $_GET[ 'filter' ] );
			foreach( $_GET[ 'filter' ] as $filter ) {
				foreach ( explode( ' ', $filter ) as $part ) {
					$models = $models->filter_name_contains( $part );
				}
			}
		}
		$template->models = $models;

		// Pagination
		$template->pagination = new \StdClass;
		$template->pagination->size = $this->pagination_size;
		if ( isset( $_GET[ 'no_pagination' ] ) ) {
			$template->pagination->size = $template->models->count( );
		}
		$template->pagination->count = $models->count( );
		$template->pagination->page_last = ceil( $template->pagination->count / $template->pagination->size ) - 1;
		$offset = 0;
		if ( 
			isset( $_GET[ 'offset' ] ) && 
			ctype_digit( $_GET[ 'offset' ] ) &&
			$_GET[ 'offset' ] <= $template->pagination->page_last
		) {
			$offset = $_GET[ 'offset' ];
		}
		$template->pagination->offset = $offset;
		$template->pagination->max_page_displayed = $this->pagination_max_page_displayed;
		$side_page_displayed = round( ( $template->pagination->max_page_displayed - 1 ) / 2 );
		$template->pagination->page_start = ( $template->pagination->offset >= $side_page_displayed ) ? $template->pagination->offset - $side_page_displayed:0;
		$template->pagination->page_end = ( $template->pagination->page_last >= $side_page_displayed + $template->pagination->offset ) ? $template->pagination->offset + $side_page_displayed:$template->pagination->page_last;
		$template->pagination->start = $template->pagination->offset * $template->pagination->size;
		$template->pagination->end = $template->pagination->start + $template->pagination->size;

		// Done!
		$template->base_get_parameter = '?' . http_build_query( $_GET );
		$template->model_subtypes = $this->get_controller( )->get_subtype( );
		return $template;
	}

}

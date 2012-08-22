<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\View\Module\Model;

abstract class All extends All\__Parent {


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
			$template->filter = $_GET[ 'filter' ];
			$models = $models->filter_name_contains( $_GET[ 'filter' ] );
		}
		$template->models = $models;

		// Pagination
		$template->pagination = new \StdClass;
		$template->pagination->size = 15;
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
		$template->pagination->page_displayed = 7;
		$side_page_displayed = round( ( $template->pagination->page_displayed - 1 ) / 2 );
		$template->pagination->page_start = ( $template->pagination->offset >= $side_page_displayed ) ? $template->pagination->offset - $side_page_displayed:0;
		$template->pagination->page_end = ( $template->pagination->page_last >= $side_page_displayed + $template->pagination->offset ) ? $template->pagination->offset + $side_page_displayed:$template->pagination->page_last;
		$template->pagination->start = $template->pagination->offset * $template->pagination->size;
		$template->pagination->end = $template->pagination->start + $template->pagination->size;
		$template->models = $template->models->slice( $template->pagination->start, $template->pagination->size );

		// Done!
		$template->model_subtypes = $this->get_controller( )->get_subtype( );
		return $template;
	}

}

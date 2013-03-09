<?php

/* This file is part of the Ubiq project, which is under MIT license */

namespace Staq\Core\Router\Stack\Util;

class FormHelper {



	/*************************************************************************
	  ATTIBUTES
	 *************************************************************************/
	const REQUIRED = 'required';

	protected $fields = array( );
	protected $values = array( );
	protected $constraints = array( );
	protected $messages = array( );
	protected $errors = array( );
	protected $isTreated = FALSE;
	protected $isActif = FALSE;
	protected $isValid = FALSE;



	/*************************************************************************
	  SETTER METHODS
	 *************************************************************************/
	public function setFields( ) {
		$fields = func_get_args( );
		foreach ( $fields as $fieldPath ) {
			if ( \UString::has( $fieldPath, ' as ' ) ) {
				$fieldPath = UString::substrBefore( $fieldPath, ' as ' );
				$fieldName = UString::substrAfter( $fieldPath, ' as ' );
			} else {
				$fieldName = $fieldPath;
			}
			$this->addField( $fieldPath, $fieldName );
		}
		return $this;
	}

	public function addField( $fieldPath, $fieldName = NULL ) {
		if ( is_null( $fieldName ) ) {
			$fieldName = $fieldPath;
		}
		$this->fields[ $fieldPath ] = $fieldName;
		$this->values[ $fieldPath ] = NULL;
		$this->constraints[ $fieldPath ] = array( );
		$this->messages[ $fieldPath ] = array( );
		$this->error[ $fieldPath ] = array( );
		return $this;
	}
	
	public function addConstraint( $fields, $constraint, $errorMessage = NULL ) {
		$constraint = new \Stack\Util\FormConstraint( $constraint, $errorMessage );
		\UArray::doConvertToArray( $fields );
		foreach ( $fields as $field ) {
			if ( ! isset( $this->constraints[ $field ] ) ) {
				throw new \Exception( 'Unknown form field: ' . $field );
			}
			$this->constraints[ $field ][ ] = $constraint;
		}
		return $this;
	}



	/*************************************************************************
	  GETTER METHODS
	 *************************************************************************/
	public function isValid( ) {
		$this->treat( );
		return $this->isValid;
	}

	public function getValues( ) {
		$this->treat( );
		return $this->values;
	}

	public function getErrors( ) {
		$this->treat( );
		return $this->messages;
	}



	/*************************************************************************
	  PRIVATE METHODS
	 *************************************************************************/
	protected function initSubmitedValues( ) {
		if ( $this->isTreated ) {
			return NULL;
		}
		foreach( $this->fields as $path => $name ) {
			if ( \Pixel418\Iniliq::hasDeepSelector( $_POST, $path ) ) {
				$this->isActif = TRUE;
				$value = \Pixel418\Iniliq::getDeepSelector( $_POST, $path );
				$this->values[ $name ] = $value;
			}
		}
	}
	protected function treat( ) {
		if ( $this->isTreated ) {
			return NULL;
		}
		$this->initSubmitedValues( );
		$this->isTreated = TRUE;
		if ( ! $this->isActif ) {
			return NULL;
		}
		$this->isValid = TRUE;
		foreach( $this->constraints as $field => $constraints ) {
			foreach( $constraints as $constraint ) {
				if ( ! $constraint->test( $this->values[ $field ] ) ) {
					$this->messages[ $field ][ ] = $constraint->getMessage( );
					$this->isValid = FALSE;
				}
			}
		}
	}
}
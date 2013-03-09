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
				$fieldName = \UString::substrAfter( $fieldPath, ' as ' );
				$fieldPath = \UString::substrBefore( $fieldPath, ' as ' );
			} else {
				$fieldName = NULL;
			}
			$this->addField( $fieldPath, $fieldName );
		}
		return $this;
	}

	public function addField( $fieldPath, $fieldName = NULL ) {
		$this->errors[ $fieldPath ] = array( );
		$this->values[ $fieldPath ] = NULL;
		if ( is_null( $fieldName ) ) {
			$fieldName = $fieldPath;
		} else {
			$this->values[ $fieldName ] = NULL;
			$this->errors[ $fieldName ] = array( );
		}
		$this->fields[ $fieldPath ] = $fieldName;
		$this->constraints[ $fieldName ] = array( );
		return $this;
	}
	
	public function addConstraint( $fields, $constraint, $errorMessage = NULL ) {
		$constraint = new \Stack\Util\FormConstraint( $constraint, $errorMessage );
		\UArray::doConvertToArray( $fields );
		foreach ( $fields as $field ) {
			if ( $this->isFieldPath( $field ) ) {
				$field = $this->getFieldName( $field );
			}
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
		return $this->errors;
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
				if ( $this->isFieldName( $name ) ) {
					$this->values[ $this->getFieldPath( $name ) ][ ] = $value;
				} else if ( $this->isFieldPath( $name ) ) {
					$this->values[ $this->getFieldName( $name ) ][ ] = $value;
				}
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
		foreach( $this->fields as $field ) {
			foreach( $this->constraints[ $field ] as $constraint ) {
				if ( ! $constraint->test( $this->values[ $field ] ) ) {
					$this->errors[ $field ][ ] = $constraint->getMessage( );
					if ( $this->isFieldName( $field ) ) {
						$this->errors[ $this->getFieldPath( $field ) ][ ] = $constraint->getMessage( );
					} else if ( $this->isFieldPath( $field ) ) {
						$this->errors[ $this->getFieldName( $field ) ][ ] = $constraint->getMessage( );
					}
					$this->isValid = FALSE;
				}
			}
		}
	}
	protected function isFieldPath( $field ) {
		return ( isset( $this->fields[ $field ] ) && $this->fields[ $field ] != $field );
	}
	protected function isFieldName( $field ) {
		return ( in_array( $field, $this->fields ) && ! isset( $this->fields[ $field ] ) );
	}
	protected function getFieldName( $fieldPath ) {
		return $this->fields[ $fieldPath ];
	}
	protected function getFieldPath( $fieldName ) {
		return array_search( $fieldName, $this->fields );
	}
}
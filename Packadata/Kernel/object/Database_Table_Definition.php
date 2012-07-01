<?php

/* This file is part of the Supersoniq project.
 * Supersoniq is a free and unencumbered software released into the public domain.
 * For more information, please refer to <http://unlicense.org/>
 */

namespace Supersoniq\Packadata\Kernel\Object;

abstract class Database_Table_Definition {


	/*************************************************************************
	  ATTRIBUTES                 
	 *************************************************************************/
	public $id_field = 'id';
	public $table_fields = array( );
	public $table_name;
}

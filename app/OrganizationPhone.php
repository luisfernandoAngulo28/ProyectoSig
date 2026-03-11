<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationPhone extends Model {
	
	protected $table = 'organization_phones';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
	);

	/* Updating rules */
	public static $rules_edit = array(
	);
}
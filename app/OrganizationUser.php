<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationUser extends Model {
	
	protected $table = 'organization_users';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
	);

	/* Updating rules */
	public static $rules_edit = array(
	);
}
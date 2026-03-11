<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserContact extends Model {
	
	protected $table = 'user_contacts';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
	);

	/* Updating rules */
	public static $rules_edit = array(
	);
}
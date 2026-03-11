<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupportQuestion extends Model {
	
	protected $table = 'support_questions';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
	);

	/* Updating rules */
	public static $rules_edit = array(
	);
}
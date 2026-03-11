<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RideReport extends Model {
	
	protected $table = 'ride_reports';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
	);

	/* Updating rules */
	public static $rules_edit = array(
	);
}
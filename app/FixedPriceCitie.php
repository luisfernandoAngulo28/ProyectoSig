<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FixedPriceCitie extends Model {
	
	protected $table = 'fixed_price_cities';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
	);

	/* Updating rules */
	public static $rules_edit = array(
	);
}
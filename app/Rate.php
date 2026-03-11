<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model {
	
	protected $table = 'rates';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
	);

	/* Updating rules */
	public static $rules_edit = array(
	);

	public function city() {
        return $this->belongsTo('Solunes\Business\App\City', 'city_id');
    }
}
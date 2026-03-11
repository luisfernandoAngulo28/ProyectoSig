<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverRequest extends Model {
	
	protected $table = 'driver_requests';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
	);

	/* Updating rules */
	public static $rules_edit = array(
	);

	public function driver() {
        return $this->hasOne('\App\Driver', 'id', 'driver_id');
    }
	
	public function request() {
        return $this->hasOne('\App\Driver', 'id', 'request_id');
    }
}
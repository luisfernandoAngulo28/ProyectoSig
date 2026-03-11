<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverDeviceCode extends Model {
	
	protected $table = 'driver_device_code';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
	);

	/* Updating rules */
	public static $rules_edit = array(
	);

	public function parent() {
        return $this->hasOne('\App\Driver', 'id', 'parent_id');
    }
	public function driver() {
        return $this->hasOne('\App\Driver', 'id', 'parent_id');
    }
	
}
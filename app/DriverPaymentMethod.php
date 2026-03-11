<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverPaymentMethod extends Model {
	
	protected $table = 'driver_payment_methods';
	protected $fillable = ['payment_method_id'];
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'payment_method_id' => 'required'
	);

	/* Updating rules */
	public static $rules_edit = array(
	);

	public function parent() {
		return $this->hasOne('App\Driver', 'id', 'parent_id');
	}

	public function payment_method() {
		return $this->hasOne('Solunes\Payments\App\PaymentMethod', 'id', 'payment_method_id');
	}
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{

	protected $table = 'rides';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array();

	/* Updating rules */
	public static $rules_edit = array();


	public function driver()
	{
		return $this->belongsTo('\App\Driver', 'driver_id');
	}

	public function parent()
	{
		return $this->belongsTo('\App\Request', 'parent_id');
	}

	

	public function chat()
	{
		return $this->belongsTo('\App\User', 'chat_id');
	}

	public function product_bridge()
	{
		return $this->belongsTo('Solunes\Business\App\ProductBridge', 'product_bridge_id');
	}

	public function sale()
	{
		return $this->belongsTo('Solunes\Sales\App\Sale', 'sale_id');
	}
}

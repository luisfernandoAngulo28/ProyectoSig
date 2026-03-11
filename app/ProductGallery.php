<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model {
	
	protected $table = 'product_galleries';
	public $timestamps = true;
    
	/* Creating rules */
	public static $rules_create = array(
		'product_id'=>'required',
		'image'=>'required',
	);
	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'product_id'=>'required',
		'image'=>'required',
	);

	public function product() {
        return $this->belongsTo('\Solunes\Product\App\Product');
    }
}
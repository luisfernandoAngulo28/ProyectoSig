<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialNetwork extends Model {
	
	protected $table = 'social_networks';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
		'code'=>'required',
		'url'=>'required'
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'code'=>'required',
		'url'=>'required'
	);

}
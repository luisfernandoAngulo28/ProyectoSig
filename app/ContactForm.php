<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactForm extends Model {
	
	protected $table = 'contact_forms';
	public $timestamps = true;
	
	/* Sending rules */
	public static $rules_send = array(
		'name'=>'required',
		'email'=>'required',
	);

	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
		'email'=>'required',
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
		'email'=>'required',
	);
	
}
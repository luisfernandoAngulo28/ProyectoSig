<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FirebaseNotification extends Model {
	
	protected $table = 'firebase_notifications';
	public $timestamps = true;

	/* Creating rules */
	public static $rules_create = array(
	);

	/* Updating rules */
	public static $rules_edit = array(
	);
}
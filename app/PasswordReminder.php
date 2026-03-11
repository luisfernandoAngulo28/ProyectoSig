<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordReminder extends Model {
	
	protected $table = 'password_resets';
	public $timestamps = false;

	/* Password Reminder rules */
	public static $rules_reminder = array(
		'email'=>'required|exists:users,email',
	);

	/* Change Password rules */
	public static $rules_reminder_update = array(
		'password'=>'required|min:6|confirmed',
		'password_confirmation'=>'required|min:6',
	);
	
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Information extends Model {
	
	protected $table = 'informations';
	public $timestamps = true;

    public $translatedAttributes = ['slug','name','content'];
    protected $fillable = ['slug','name','content'];

    use Translatable;
    
	/* Creating rules */
	public static $rules_create = array(
		'name'=>'required',
		'content'=>'required',
	);
	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'name'=>'required',
		'content'=>'required',
	);
}
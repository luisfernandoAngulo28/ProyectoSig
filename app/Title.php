<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Solunes\Master\App\Traits\Section;
use Dimsav\Translatable\Translatable;

class Title extends Model {
	
	protected $table = 'titles';
	public $timestamps = true;

    public $translatedAttributes = ['name'];
    protected $fillable = ['name'];

    use Section, Translatable;

	/* Creating rules */
	public static $rules_create = array(
		'code'=>'required',
		'name'=>'required'
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'code'=>'required',
		'name'=>'required'
	);

}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Solunes\Master\App\Traits\Section;
use Dimsav\Translatable\Translatable;

class Content extends Model {
	
	protected $table = 'contents';
	public $timestamps = true;

    public $translatedAttributes = ['content'];
    protected $fillable = ['content'];

    use Section, Translatable;

	/* Creating rules */
	public static $rules_create = array(
		'code'=>'required',
		'content'=>'required'
	);

	/* Updating rules */
	public static $rules_edit = array(
		'id'=>'required',
		'code'=>'required',
		'content'=>'required'
	);
	
}
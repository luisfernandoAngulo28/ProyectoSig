<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TitleTranslation extends Model {
	
	protected $table = 'title_translation';
    public $timestamps = false;
    protected $fillable = ['name'];
	
}
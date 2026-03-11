<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentTranslation extends Model {
	
	protected $table = 'content_translation';
    public $timestamps = false;
    protected $fillable = ['content'];
	
}
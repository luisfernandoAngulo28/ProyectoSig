<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class InformationTranslation extends Model {
	
	protected $table = 'information_translation';
    public $timestamps = false;
    protected $fillable = ['slug','name','content'];

    use Sluggable, SluggableScopeHelpers;
    public function sluggable(){
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
	   
    public function information() {
        return $this->belongsTo('App\Information');
    }
}
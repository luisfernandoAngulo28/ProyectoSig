<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sindicato extends Model {

    protected $table = 'sindicatos';
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
    );

    /* Edit rules */
    public static $rules_edit = array(
    );

    public function organizations() {
        return $this->hasMany('\App\Organization', 'sindicato_id', 'id');
    } 
    public function city() {
        return $this->belongsTo('Solunes\Business\App\City');
    }
    public function users() {
        return $this->hasMany('\App\User', 'sindicato_id', 'id');
    }
}
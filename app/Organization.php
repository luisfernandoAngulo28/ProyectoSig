<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model {

    protected $table = 'organizations';
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
        'name'  =>'required|max:255',
        'city_id'  =>'required',
        'type'  =>'required',
      
    );

    /* Edit rules */
    public static $rules_edit = array(
        'name'  =>'required|max:255',
        'city_id'  =>'required',
        'type'  =>'required',
       
    );

    public function organization_users() {
        return $this->belongsToMany('\App\User','organization_users');
    } 

    public function organization_phones() {
        return $this->hasMany('\App\OrganizationPhone', 'parent_id', 'id');
    } 

    public function city() {
        return $this->belongsTo('Solunes\Business\App\City');
    }

    public function sindicato() {
        return $this->belongsTo('\App\Sindicato');
    }

    public function request() {
        return $this->hasMany('App\Request', 'taxi_company');
    }


}
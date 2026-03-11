<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model {

    protected $table = 'requests';
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
    );

    /* Edit rules */
    public static $rules_edit = array(
    );

    public function type_request() {
        return $this->hasOne('\App\TypeRequest', 'id', 'type_request_id');
    }

    public function user() {
        return $this->hasOne('\App\User', 'id', 'user_id');
    }

    public function request_waypoints() {
        return $this->hasMany('\App\RequestWaypoint', 'parent_id', 'id');
    }

    public function request_trips() {
        return $this->hasMany('\App\RequestTrip', 'parent_id', 'id');
    }

    public function rides() {
        return $this->hasMany('\App\Ride', 'parent_id', 'id');
    }

    public function payment_method() {
        return $this->belongsTo('Solunes\Payments\App\PaymentMethod');
    }

    public function getNameAttribute() {
        return $this->attributes['id'];
    }

    public function request_drivers() {
        return $this->belongsToMany('\App\Driver','driver_requests');
    } 

    public function taxi_company() {
        return $this->belongsTo('App\Organization', 'taxi_company');
    }

    public function user_id() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function driver_requests() {
        return $this->hasMany('\App\DriverVehicle', 'request_id', 'id');
    } 
}
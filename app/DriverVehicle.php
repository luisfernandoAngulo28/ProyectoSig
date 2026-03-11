<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverVehicle extends Model {

    protected $table = 'driver_vehicles';
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
        // 'city_id' => 'required',
        'number_plate' => 'required|max:255',
        // 'vehicle_image' => 'required',
        // 'vehicle_brand_id' => 'required',
        // 'vehicle_model_id' => 'required',
        // 'color' => 'required',
    );

    /* Edit rules */
    public static $rules_edit = array(
        // 'city_id' => 'required',
        'number_plate' => 'required|max:255',
        // 'vehicle_image' => 'required',
        // 'vehicle_brand_id' => 'required',
        // 'vehicle_model_id' => 'required',
        // 'color' => 'required',
    );

    public function driver() {
        return $this->hasOne('\App\Driver', 'id', 'parent_id');
    }

    public function parent() {
        return $this->hasOne('\App\Driver', 'id', 'parent_id');
    }

    public function vehicle_model() {
        return $this->hasOne('\App\VehicleModel', 'id', 'vehicle_model_id');
    }

    public function vehicle_brand() {
        return $this->hasOne('\App\VehicleBrand', 'id', 'vehicle_brand_id');
    }

    public function city() {
        return $this->belongsTo('Solunes\Business\App\City');
    }
}
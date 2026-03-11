<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model {

    protected $table = 'vehicle_models';
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
    );

    /* Edit rules */
    public static $rules_edit = array(
    );

    public function vehicle_brand() {
        return $this->hasOne('\App\VehicleBrand', 'id', 'vehicle_brand_id');
    }
}
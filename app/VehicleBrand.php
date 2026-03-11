<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleBrand extends Model {

    protected $table = 'vehicle_brands';
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
    );

    /* Edit rules */
    public static $rules_edit = array(
    );

}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverActivation extends Model {

    protected $table = 'driver_activations';
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
    );

    /* Edit rules */
    public static $rules_edit = array(
    );

    public function driver() {
        return $this->hasOne('\App\Driver', 'id', 'parent_id');
    }

    public function parent() {
        return $this->hasOne('\App\Driver', 'id', 'parent_id');
    }
}
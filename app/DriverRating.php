<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DriverRating extends Model {

    protected $table = 'driver_ratings';
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

    public function user() {
        return $this->hasOne('\App\User', 'id', 'user_id');
    }
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRating extends Model {

    protected $table = 'user_ratings';
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
    );

    /* Edit rules */
    public static $rules_edit = array(
    );

    public function parent() {
        return $this->belongsTo('\App\User');
    }

    public function user() {
        return $this->belongsTo('\App\User', 'parent_id');
    }

    public function driver() {
        return $this->hasOne('\App\Driver', 'id', 'driver_id');
    }
}
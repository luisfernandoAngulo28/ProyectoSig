<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestWaypoint extends Model {

    protected $table = 'request_waypoints';
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
    );

    /* Edit rules */
    public static $rules_edit = array(
    );

    public function request() {
        return $this->hasOne('\App\Request', 'id', 'parent_id');
    }

    public function parent() {
        return $this->hasOne('\App\Request', 'id', 'parent_id');
    }
}
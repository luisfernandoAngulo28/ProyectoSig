<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model {

    protected $table = 'otps';
    protected $appends = ['parent_id'];
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
    );

    /* Edit rules */
    public static $rules_edit = array(
    );

    public function parent() {
        return $this->hasOne('\App\User', 'id', 'parent_id');
    }
}
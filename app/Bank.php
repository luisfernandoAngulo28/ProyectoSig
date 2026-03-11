<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model {

    protected $table = 'banks';
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
    );

    /* Edit rules */
    public static $rules_edit = array(
    );

}
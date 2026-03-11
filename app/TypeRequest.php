<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeRequest extends Model {

    protected $table = 'type_requests';
    public $timestamps = true;

    /* Create rules */
    public static $rules_create = array(
    );

    /* Edit rules */
    public static $rules_edit = array(
    );

}
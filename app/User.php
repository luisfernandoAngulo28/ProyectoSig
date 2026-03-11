<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Solunes\Master\App\Traits\UserPermission;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{

    use Authenticatable, Authorizable, CanResetPassword, SoftDeletes, UserPermission;

    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['remember_token'];
    protected $dates = ['deleted_at', 'last_login'];

    /* Login rules */
    /* Login rules */
    public static $rules_login = array(
        'user' => 'required',
        'password' => 'required|min:6',
    );

    /* Edit Pass rules */
    public static $rules_edit_pass = array(
        'password' => 'required|min:6|confirmed',
        'password_confirmation' => 'required|min:6',
    );

    /* Creating rules */
    public static $rules_create = array(
        'email' => 'email|required_without_all:cellphone,username|unique:users',
        'cellphone' => 'required_without_all:email,username',
        //'username' => 'required_without_all:email,cellphone',
        'password' => 'required|min:6',
        'name' => 'required',
        //'status' => 'required',
        // 'date_birth' => 'required',
        //'notifications_email' => 'required',
        //'role_user' => 'required',
        //'is_connect' => 'required',
        //'type' => 'required',
        //'is_verify' => 'required',

    );

    /* Edit rules */
    public static $rules_edit = array(
        'email' => 'email|required',
        'cellphone' => 'required',

        //'username' => 'required_without_all:email,cellphone',
        //'password' => 'required|min:6',
        //'name' => 'required',
        //'status' => 'required',
        // 'date_birth' => 'required',
        //'notifications_email' => 'required',
        //'role_user' => 'required',
        //'is_connect' => 'required',
        //'type' => 'required',
        //'is_verify' => 'required',
    );

    public function site()
    {
        return $this->belongsTo('Solunes\Master\App\Site');
    }

    public function city()
    {
        return $this->belongsTo('Solunes\Business\App\City');
    }

    public function agency()
    {
        return $this->belongsTo('Solunes\Business\App\Agency');
    }

    public function provider()
    {
        return $this->belongsTo('Solunes\Business\App\Agency');
    }

    public function customer()
    {
        return $this->hasOne('Solunes\Customer\App\Customer');
    }

    public function customers()
    {
        return $this->hasMany('Solunes\Customer\App\Customer');
    }

    public function payments()
    {
        return $this->hasMany('Solunes\Payments\App\Payment');
    }

    public function pending_payments()
    {
        return $this->hasMany('Solunes\Payments\App\Payment')->where('status', 'holding');
    }

    public function activities()
    {
        return $this->hasMany('Solunes\Master\App\Activity');
    }

    public function notifications()
    {
        return $this->hasMany('Solunes\Master\App\Notification')->orderBy('created_at', 'DESC');
    }

    public function setPasswordAttribute($value)
    {
        if ($value !== null) $this->attributes['password'] = strlen($value) >= 32 ? $value : bcrypt($value);
    }

    public function user_ratings()
    {
        return $this->hasMany('\App\UserRating', 'parent_id');
    }

    public function otps()
    {
        return $this->hasMany('\App\Otp', 'parent_id', 'id');
    }

    public function user_contacts()
    {
        return $this->hasMany('\App\UserContact', 'parent_id', 'id');
    }

    public function organization()
    {
        return $this->hasOne('\App\Organization', 'id', 'organization_id');
    }

    public function driver()
    {
        return $this->hasOne('\App\Driver', 'user_id', 'id');
    }

    public function panic_buttons()
    {
        return $this->hasMany('\App\PanicButton', 'parent_id', 'id');
    }

    public function sindicato()
    {
        return $this->belongsTo('\App\Sindicato');
    }

    public function session()
    {
        return $this->belongsTo('\App\Sindicato');
    }

    public function requests()
    {
        return $this->hasMany('\App\Request', 'user_id', 'id');
    }

    public function role_user()
    {
        return $this->belongsToMany('\App\Role', 'role_user', 'user_id', 'role_id');
    }

    public function rides()
    {
        return $this->hasMany('\App\Ride', 'user_id', 'id');
    }

    // protected static function boot() {
    //     static::updated(function($user) {
    //         $userFind = \App\User::where('id', $user->id)->first();
    //         if($user->password == '' || $user->password == null){
    //             $user->password = $userFind->password;
    //         }
    //     });
    // }
}

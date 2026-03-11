<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model {

    protected $table = 'drivers';
    public $timestamps = true;


    /* Create rules */
    public static $rules_create = array(
        'city_id' => 'required',
        'email'           => 'required|unique:users',
        'cellphone'       => 'required|min:6',
        'organization_id' => 'required',
        'image'           => 'required|max:255',
        'license_number'  => 'required|max:255',
        'license_back_image'     => 'required|max:255',
        'license_front_image'    => 'required|max:255',
        'ci_back_image'          => 'required|max:255',
        'ci_front_image'         => 'required|max:255',
        'number_of_passengers'   => 'required|numeric',
        'active_trips'      => 'required',
        'car_with_grill'    => 'required',
        'travel_with_pets'  => 'required',
        'first_name'  => 'required',
        'last_name'  => 'required',
    );

    /* Edit rules */
    public static $rules_edit = array(
        'city_id' => 'required',
        'email'           => 'required',
        'cellphone'       => 'required|min:6',
        'organization_id' => 'required',
        'image'           => 'required|max:255',
        'license_number'  => 'required|max:255',
        'license_back_image'          => 'required|max:255',
        'license_front_image'         => 'required|max:255',
        'ci_back_image'          => 'required|max:255',
        'ci_front_image'         => 'required|max:255',
        'number_of_passengers'        => 'required|numeric',
        'active_trips'      => 'required',
        'first_name'  => 'required',
        'last_name'  => 'required',
    );

    public function user() {
        return $this->hasOne('\App\User', 'id', 'user_id');
    } 

    public function user_belongs_to() {
        return $this->hasOne('\App\User', 'id', 'user_belongs_to_id');
    } 

    public function organization() {
        return $this->hasOne('\App\Organization', 'id', 'organization_id');
    } 
    public function bank() {
        return $this->belongsTo('\App\Bank');
    } 

    public function driver_vehicles() {
        return $this->hasMany('\App\DriverVehicle', 'parent_id', 'id');
    } 

    public function driver_ratings() {
        return $this->hasMany('\App\DriverRating', 'parent_id', 'id');
    }

    public function driver_activations() {
        return $this->hasMany('\App\DriverActivation', 'parent_id', 'id');
    }
    
    public function driver_device_code() {
        return $this->hasMany('\App\DriverDeviceCode', 'parent_id', 'id');
    }

    public function getNameAttribute() {
        return sprintf('%sNúmero de Licencia: %s', $this->user ? $this->user->name . ' - ' : '', $this->attributes['license_number'] );
    }

    public function city() {
        return $this->belongsTo('Solunes\Business\App\City');
    }

    public function driver_payment_methods() {
        return $this->hasMany('\App\DriverPaymentMethod', 'parent_id', 'id');
    }

    protected static function boot() {
        parent::boot();
        static::created(function($driver) {
            $payment_methods = \Solunes\Payments\App\PaymentMethod::get();
            foreach( $payment_methods as $payment_method )  {
                $driver_payment_method = new \App\DriverPaymentMethod;
                $driver_payment_method->parent_id         = $driver->id;
                $driver_payment_method->payment_method_id = $payment_method->id;
                $driver_payment_method->active            = 1;
                $driver_payment_method->save();
            }
        });

        static::updated(function($driver) {
         
            $user = \App\User::where('id', $driver->user_id)->first();
            $user->name = $driver->first_name . ' ' . $driver->last_name;
            $user->first_name = $driver->first_name;
            $user->last_name = $driver->last_name;
            $user->city_id = $driver->city_id;
            $user->email = $driver->email;
            $user->cellphone = $driver->cellphone;
            $user->is_verify = 1;
            $user->save();
        });
    }

    // RIDES 
    public function rides() {
        return $this->hasMany('App\Ride', 'driver_id');
    }

    public function driver_requests() {
        return $this->hasMany('\App\DriverVehicle', 'driver_id', 'id');
    } 
}



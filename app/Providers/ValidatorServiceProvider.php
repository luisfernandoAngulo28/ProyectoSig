<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        Validator::extend('maxwords', function($attribute, $value, $parameters, $validator) {
            $words = preg_split( '@\s+@i', trim( $value ) );
            if ( count( $words ) <= $parameters[ 0 ] ) {
                return true;
            }
            return false;
        });
        Validator::replacer('maxwords', function($message, $attribute, $rule, $parameters) {
            return str_replace(':maxwords', $parameters[0], $message);
        });
        Validator::extend('limitdontapply', function($attribute, $value, $parameters, $validator) {
            //$team_b = array_get($validator->getData(), $parameters[0]);  
            $values = $validator->getData();
            $counts = array_count_values(array_flatten(array_values($values)));
            if ( !isset($counts['dont_apply']) || $counts['dont_apply'] <= $parameters[ 0 ] ) {
                return true;
            }
            return false;
        });
        Validator::replacer('limitdontapply', function($message, $attribute, $rule, $parameters) {
            return str_replace(':limitdontapply', $parameters[0], $message);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){
        //
    }
}
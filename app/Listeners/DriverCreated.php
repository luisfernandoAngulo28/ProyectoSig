<?php

namespace App\Listeners;

class DriverCreated
{

    /**
     * Handle the event.
     *
     * @param  PodcastWasPurchased  $event
     * @return void
     */
    public function handle($event) {
        if( empty( $event->user_id ) ) {
            $customer = \Customer::generateCustomer(null, $event->email, ['first_name'=>$event->cellphone, 'last_name'=>$event->email], $event->cellphone);
            $update   = \App\Driver::where('id', $event->id)->update(['user_id'=> $customer->user->id]);
            $user = $customer->user;

            $user->name = $event->first_name . ' ' . $event->last_name;
            $user->first_name = $event->first_name;
            $user->last_name = $event->last_name;
            $user->email = $event->email;
            $user->cellphone = $event->cellphone;
            $user->city_id = $event->city_id;
            $user->is_verify = 1;
            $user->save();
            $user->role_user()->detach(2);
            $user->role_user()->attach(5);

            $event->movil_number = 0;
            $event->baby_chair = false;
            $event->fragile_content = false;
            $event->model_year =  $event->model_year !== null ?  $event->model_year : '';
        }
        return $event;
    }
}


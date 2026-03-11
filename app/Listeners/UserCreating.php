<?php

namespace App\Listeners;

class UserCreating
{

    /**
     * Handle the event.
     *
     * @param  PodcastWasPurchased  $event
     * @return void
     */
    public function handle($event) {
        // if( $event->map ) {
        //     $latitudes = explode(';',$event->map);
        //     $latitude = trim($latitudes[0]);
        //     $longitude = trim($latitudes[1]);
        //     $event->latitude = $latitude;
        //     $event->longitude = $longitude;
        //     $event->longitude = $longitude;
        // }
        $event->name = $event->first_name .' '.$event->last_name;

        return $event;
    }
}


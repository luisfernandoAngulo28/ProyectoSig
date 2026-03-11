<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Solunes\Master\App\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        /*'Illuminate\Auth\Events\Login' => [
            'Solunes\Master\App\Listeners\UserLoggedIn',
        ],*/
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        //$events->listen('eloquent.saved: Solunes\Product\App\Product', '\App\Listeners\ProductSaved');
        $events->listen('eloquent.saving: App\Organization', '\App\Listeners\OrganizationSaving');
        $events->listen('eloquent.saving: Solunes\Customer\App\Customer', '\Solunes\Customer\App\Listeners\CustomerSaving');
        $events->listen('eloquent.saving: Solunes\Business\App\ProductBridge', '\Solunes\Business\App\Listeners\ProductBridgeSaving');

        $events->listen('eloquent.created: App\Driver', '\App\Listeners\DriverCreated');
        // $events->listen('eloquent.updated: App\Driver', '\App\Listeners\DriverCreated');

        $events->listen('eloquent.creating: App\User', '\App\Listeners\UserCreating');
        $events->listen('eloquent.updating: App\User', '\App\Listeners\UserCreating');
        parent::boot($events);
    }
}

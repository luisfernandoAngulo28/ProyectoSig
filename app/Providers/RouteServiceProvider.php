<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Solunes\Master\App\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        //

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $this->mapAdminRoutes($router);
        $this->mapWebRoutes($router);
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapAdminRoutes(Router $router)
    {
        $router->group(['namespace' => $this->namespace, 'middleware' => 'admin'], function ($router) {
            require app_path('Http/Routes/artisan.php');
            require app_path('Http/Routes/admin.php');
        });
        parent::mapAdminRoutes($router);
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapWebRoutes(Router $router)
    {
    
        $router->group(['namespace' => $this->namespace, 'middleware' => 'web'], function ($router) {
            require app_path('Http/Routes/api.php');
            require app_path('Http/Routes/routes.php');
        });
        parent::mapWebRoutes($router);
    }

}

<?php

namespace App\Providers;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Solunes\Master\App\Providers\ComposerServiceProvider as ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{

    public function boot(ViewFactory $view)
    {
        view()->composer(['layouts.master', 'layouts.master-clean', 'master::layouts.admin', 'master::layouts.admin-2'], function ($view) {
            $array['footer_name'] = \FuncNode::check_var('footer_name');
            $array['footer_rights'] = \FuncNode::check_var('footer_rights');
            $array['cart'] = \Sales::get_cart();
            $array['menus'] = \Solunes\Master\App\Menu::where('level',1)->where('menu_type','site')->get();
            $array['social'] = \App\SocialNetwork::get();
            $array['infos'] = \App\Information::get();
            $array['informations'] = \App\Information::get();
            $array['categories_master'] = \Solunes\Business\App\Category::get();
            $view->with($array);
        });
        view()->composer(['layouts.master-clean'], function ($view) {
            $array['site'] = \Solunes\Master\App\Site::first();
            $view->with($array);
        });
        parent::boot($view);
    }

    public function register()
    {
        //
    }

}
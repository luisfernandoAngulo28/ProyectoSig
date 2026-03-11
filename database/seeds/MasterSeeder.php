<?php

use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // General
        $site = \Solunes\Master\App\Site::find(1);
        $site->name = 'Taxis';
        $site->domain = 'https://taxisapp.solunes.site/';
        $site->title = 'Taxis';
        $site->description = 'Bienvenidos al Sistema de TAXIS APP. ';
        $site->keywords = 'taxis, app, tecnología, drivers, conductores, pagos';
        $site->google_verification = '';
        $site->analytics = '';
        $site->save();
        
        // Nodos
        $node_social_network = \Solunes\Master\App\Node::create(['name'=>'social-network', 'location'=>'app', 'folder'=>'global']);
        $node_title = \Solunes\Master\App\Node::create(['name'=>'title']);
        $node_content = \Solunes\Master\App\Node::create(['name'=>'content']);
        $node_contact_form = \Solunes\Master\App\Node::create(['name'=>'contact-form', 'folder'=>'form']);


        // Menu: Home
        $page_home = \Solunes\Master\App\Page::create(['type'=>'customized', 'customized_name'=>'home', 'es'=>['name'=>'Inicio']]);
        \Solunes\Master\App\Menu::create(['page_id'=>$page_home->id]);

        \Solunes\Master\App\Menu::create(['menu_type'=>'admin','icon'=>'table','level'=>'1','permission'=>'parameters','name'=>'Reporte de 
        Ingresos','link'=>'admin/reportes-totales']);
        \Solunes\Master\App\Menu::create(['menu_type'=>'admin','icon'=>'table','level'=>'1','permission'=>'parameters','name'=>'Reporte de calificaciones','link'=>'admin/model-list/driver-rating']);
        \Solunes\Master\App\Menu::create(['menu_type'=>'admin','icon'=>'table','level'=>'1','permission'=>'parameters','name'=>'Rep. Activaciones Conductores','link'=>'admin/model-list/driver-activation']);
        


        //TIENDA
        $page_shop = \Solunes\Master\App\Page::create(['type'=>'customized', 'customized_name'=>'shop', 'es'=>['name'=>'Productos']]);
        \Solunes\Master\App\Menu::create(['page_id'=>$page_shop->id]);

        //Menu Ayuda
        $help = \Solunes\Master\App\Menu::create(['type'=>'blank', 'es'=>['name'=>'Ayuda']]);
        \Solunes\Master\App\Menu::create(['level'=>2, 'parent_id'=>$help->id, 'es'=>['name'=>'Preguntas Frecuentes', 'link'=>'informacion/preguntas-frecuentes'], ]);
        \Solunes\Master\App\Menu::create(['level'=>2, 'parent_id'=>$help->id, 'es'=>['name'=>'Métodos de Pago', 'link'=>'informacion/metodos-de-pago'], ]);
        \Solunes\Master\App\Menu::create(['level'=>2, 'parent_id'=>$help->id, 'es'=>['name'=>'Métodos de Envío', 'link'=>'informacion/metodos-de-envio'], ]);
        \Solunes\Master\App\Menu::create(['level'=>2, 'parent_id'=>$help->id, 'es'=>['name'=>'Términos de Uso', 'link'=>'informacion/terminos-de-uso'], ]);
        //\Solunes\Master\App\Menu::create(['level'=>2, 'parent_id'=>$help->id, 'es'=>['name'=>'Sobre Libelula', 'link'=>'informacion/libelula'], ]);

        // Menu: Nosotros
        $page_about = \Solunes\Master\App\Page::create(['type'=>'customized', 'customized_name'=>'about', 'es'=>['name'=>'Nosotros'], 'en'=>['name'=>'Nosotros']]);
        \Solunes\Master\App\Menu::create(['page_id'=>$page_about->id]);

        // Menu: Contacto
        $page_contact = \Solunes\Master\App\Page::create(['type'=>'customized', 'customized_name'=>'contact', 'es'=>['name'=>'Contacto'], 'en'=>['name'=>'Contact']]);
        \Solunes\Master\App\Menu::create(['page_id'=>$page_contact->id]);

        // Nodos App taxis
        $node_user              = \Solunes\Master\App\Node::where('name','user')->first();
        $node_information       = \Solunes\Master\App\Node::create(['name'=>'information']);
        $node_organization      = \Solunes\Master\App\Node::create(['name'=>'organization']);
        $node_organization_phone=   
        // $node_user_ratings      = \Solunes\Master\App\Node::create(['name'=>'user-rating', 'type' => 'child', 'parent_id' => $node_user->id]);

        $node_user_rating = \Solunes\Master\App\Node::create(['name'=>'user-rating', 'type' => 'child', 'parent_id' => $node_user->id, 'folder'=>'parameters', 'permission'=>'customers']);
        
        $node_user_contact      = \Solunes\Master\App\Node::create(['name'=>'user-contact', 'type' => 'child', 'parent_id' => $node_user->id]);
        $node_otp               = \Solunes\Master\App\Node::create(['name'=>'otp', 'type'=>'child', 'parent_id'=> $node_user->id]);
        $node_driver            = \Solunes\Master\App\Node::create(['name'=>'driver']);
        $node_vehicle_model     = \Solunes\Master\App\Node::create(['name'=>'vehicle-model']);  // child of brand
        $node_vehicle_brand     = \Solunes\Master\App\Node::create(['name'=>'vehicle-brand']);
        $node_driver_vehicle    = \Solunes\Master\App\Node::create(['name'=>'driver-vehicle', 'type'=>'child', 'parent_id'=>$node_driver->id]);
        $node_driver_rating     = \Solunes\Master\App\Node::create(['name'=>'driver-rating', 'type'=>'child', 'parent_id'=>$node_driver->id]);
        $node_driver_activicion = \Solunes\Master\App\Node::create(['name'=>'driver-activation', 'type'=>'child', 'parent_id'=>$node_driver->id]);
        $node_request           = \Solunes\Master\App\Node::create(['name'=>'request']);
        $node_request_waypoint  = \Solunes\Master\App\Node::create(['name'=>'request-waypoint', 'type'=>'child', 'parent_id'=>$node_request->id]);
        $node_type_request      = \Solunes\Master\App\Node::create(['name'=>'type-request']);
        $node_request_trip      = \Solunes\Master\App\Node::create(['name'=>'request-trip', 'type'=>'child', 'parent_id'=>$node_request->id]);
        $node_organization_user = \Solunes\Master\App\Node::create(['name'=>'organization-user','table_name'=>'organization_users','type'=>'field', 'model'=>'\App\User', 'parent_id'=>$node_organization->id]);

        $node_ride              = \Solunes\Master\App\Node::create(['name'=>'ride', 'parent_id'=> $node_request->id, 'type' => 'subchild']);
        
        $node_driver_request    = \Solunes\Master\App\Node::create(['name'=>'driver-request']);
        $node_ride_report       = \Solunes\Master\App\Node::create(['name'=>'ride-report']);
        $node_rate              = \Solunes\Master\App\Node::create(['name'=>'rate']);
        $node_support_question  = \Solunes\Master\App\Node::create(['name'=>'support-question']);
        $node_driver_payment_method   = \Solunes\Master\App\Node::create(['name'=>'driver-payment-method', 'type'=>'child', 'parent_id'=>$node_driver->id]);
        $node_request_bring_money     = \Solunes\Master\App\Node::create(['name'=>'request-bring-money']);
        $node_driver_device_code      = \Solunes\Master\App\Node::create(['name'=>'driver-device-code', 'table_name'=>'driver_device_code', 'type'=>'subchild', 'parent_id'=>$node_driver->id ]);
        $node_sindicato   = \Solunes\Master\App\Node::create(['name'=>'sindicato']);
        $node_panic_button   = \Solunes\Master\App\Node::create(['name'=>'panic-button', 'table_name'=>'panic_button']);
        $node_fixed_price_citie   = \Solunes\Master\App\Node::create(['name'=>'fixed-price-citie']);
        $node_firebase_notification   = \Solunes\Master\App\Node::create(['name'=>'firebase-notification']);
        $node_bank  = \Solunes\Master\App\Node::create(['name'=>'bank']);


        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'select', 'parameter'=>'city_id',  'node_id'=>$node_organization->id]);
        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'select', 'parameter'=>'city_id',  'node_id'=>$node_driver->id]);
        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'select', 'parameter'=>'parent_id','node_id'=>$node_driver_vehicle->id]);
        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'select', 'parameter'=>'active',   'node_id'=>$node_driver_vehicle->id]);
        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'select', 'parameter'=>'city_id',  'node_id'=>$node_driver_vehicle->id]);
        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'select', 'parameter'=>'city_id',  'node_id'=>$node_user->id]);

        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'select','parameter'=>'taxi_company','node_id'=>$node_request->id]);
        
        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'select','parameter'=>'type_request_id','node_id'=>$node_request->id]);

        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'date','parameter'=>'created_at','node_id'=>$node_request->id]);
        
        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'date','parameter'=>'created_at','node_id'=>$node_ride->id]);

        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'string','parameter'=>'status','node_id'=>$node_ride->id]);

        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'select','parameter'=>'taxi_company','node_id'=>$node_request->id]);

        \Solunes\Master\App\Filter::create(['category'=>'customer','display'=>'all','type'=>'field','subtype'=>'select','parameter'=>'role_user','node_id'=>$node_user->id]);
               

        // ROLES
        $admin = \Solunes\Master\App\Role::where('name', 'admin')->first();
        $member = \Solunes\Master\App\Role::where('name', 'member')->first();
        $passanger = \Solunes\Master\App\Role::create(['name'=>'passenger', 'display_name'=>'Pasajero']);
        $subadmin = \Solunes\Master\App\Role::create(['name'=>'subadmin', 'display_name'=>'Subadmin']);
        $driver = \Solunes\Master\App\Role::create(['name'=>'driver', 'display_name'=>'Driver']);
        $alcaldia = \Solunes\Master\App\Role::create(['name'=>'alcaldia', 'display_name'=>'Alcaldia']);
        $sindicato = \Solunes\Master\App\Role::create(['name'=>'sindicato', 'display_name'=>'Sindicato']);
        $empresa = \Solunes\Master\App\Role::create(['name'=>'empresa', 'display_name'=>'Empresa']);
        
        //$new_role = \Solunes\Master\App\Role::create(['name'=>'newrole', 'display_name'=>'New Role']); // MUESTRA PARA CREAR ROL

        // PERMISOS
        $form_perm = \Solunes\Master\App\Permission::where('name', 'form')->first();
        $parameters_perm = \Solunes\Master\App\Permission::where('name','parameters')->first();
        $business_perm = \Solunes\Master\App\Permission::where('name','business')->first();
        $payments_perm = \Solunes\Master\App\Permission::where('name','payments')->first();
        $user_perm = \Solunes\Master\App\Permission::where('name','user')->first();
        $sales_perm = \Solunes\Master\App\Permission::where('name','sales')->first();
        $site_perm = \Solunes\Master\App\Permission::where('name','site')->first();
        $customers_perm = \Solunes\Master\App\Permission::create(['name'=>'customers', 'display_name'=>'Socios']);
        $members_perm = \Solunes\Master\App\Permission::where('name','members')->first();
        $subadmin_perm = \Solunes\Master\App\Permission::create(['name'=>'subadmin', 'display_name'=>'Subadmin']);


        //$new_permission = \Solunes\Master\App\Permission::create(['name'=>'newpermission', 'display_name'=>'Socios']); // MUESTRA PARA CREAR PERMISO
        $user_post = \Solunes\Master\App\Permission::create(['name'=>'user post', 'display_name'=>'POST:/api/v1/users']);
        $user_get = \Solunes\Master\App\Permission::create(['name'=>'user get', 'display_name'=>'GET:/api/v1/users']);
        $user_delete = \Solunes\Master\App\Permission::create(['name'=>'user delete', 'display_name'=>'DELETE:/api/v1/users']);
        $user_patch = \Solunes\Master\App\Permission::create(['name'=>'user patch', 'display_name'=>'PATCH:/api/v1/users']);
        $user_put = \Solunes\Master\App\Permission::create(['name'=>'user put', 'display_name'=>'PUT:/api/v1/users']);

        $organization_get = \Solunes\Master\App\Permission::create(['name'=>'Organizacion get', 'display_name'=>'GET:/api/v1/organizations']);
        $type_request_get = \Solunes\Master\App\Permission::create(['name'=>'Tipo De Solicitud get', 'display_name'=>'GET:/api/v1/type-requests']);
        $type_request_post = \Solunes\Master\App\Permission::create(['name'=>'Tipo De Solicitud post', 'display_name'=>'POST:/api/v1/type-requests']);
        
        $driver_post = \Solunes\Master\App\Permission::create(['name'=>'Driver post', 'display_name'=>'POST:/api/v1/drivers']);
        $driver_get = \Solunes\Master\App\Permission::create(['name'=>'Driver get', 'display_name'=>'GET:/api/v1/drivers']);
        $driver_delete = \Solunes\Master\App\Permission::create(['name'=>'Driver delete', 'display_name'=>'DELETE:/api/v1/drivers']);
        $driver_patch = \Solunes\Master\App\Permission::create(['name'=>'Driver patch', 'display_name'=>'PATCH:/api/v1/drivers']);
        $driver_put = \Solunes\Master\App\Permission::create(['name'=>'Driver put', 'display_name'=>'PUT:/api/v1/drivers']);
        
        $driver_rating_post = \Solunes\Master\App\Permission::create(['name'=>'Driver rating post', 'display_name'=>'POST:/api/v1/driver-ratings']);
        $user_rating_post = \Solunes\Master\App\Permission::create(['name'=>'User rating post', 'display_name'=>'POST:/api/v1/user-ratings']);

        $driver_request_post = \Solunes\Master\App\Permission::create(['name'=>'Driver Request post', 'display_name'=>'POST:/api/v1/driver-request']);
        $driver_request_get = \Solunes\Master\App\Permission::create(['name'=>'Driver Request get', 'display_name'=>'GET:/api/v1/driver-request']);
        $driver_request_delete = \Solunes\Master\App\Permission::create(['name'=>'Driver Request delete', 'display_name'=>'DELETE:/api/v1/driver-request']);
        $driver_request_patch = \Solunes\Master\App\Permission::create(['name'=>'Driver Request patch', 'display_name'=>'PATCH:/api/v1/driver-request']);
        $driver_request_put = \Solunes\Master\App\Permission::create(['name'=>'Driver Request put', 'display_name'=>'PUT:/api/v1/driver-request']);
        
        $ride_report_post = \Solunes\Master\App\Permission::create(['name'=>'Reporte de Carreras post', 'display_name'=>'POST:/api/v1/ride-reports']);
        $ride_report_get = \Solunes\Master\App\Permission::create(['name'=>'Reporte de Carreras get', 'display_name'=>'GET:/api/v1/ride-reports']);
        
        $rates_post = \Solunes\Master\App\Permission::create(['name'=>'Tarifas post', 'display_name'=>'POST:/api/v1/rates']);
        
        $support_question_get = \Solunes\Master\App\Permission::create(['name'=>'Preguntas de Soporte get', 'display_name'=>'GET:/api/v1/support-questions']);
        
        $ride_put = \Solunes\Master\App\Permission::create(['name'=>'Carrera put', 'display_name'=>'PUT:/api/v1/ride']);
        $ride_patch = \Solunes\Master\App\Permission::create(['name'=>'Carrera patch', 'display_name'=>'PATCH:/api/v1/ride']);
        $ride_get = \Solunes\Master\App\Permission::create(['name'=>'Carrera get', 'display_name'=>'GET:/api/v1/ride']);
        $ride_post = \Solunes\Master\App\Permission::create(['name'=>'Carrera post', 'display_name'=>'POST:/api/v1/ride']);
        $rides_put = \Solunes\Master\App\Permission::create(['name'=>'Carreras put', 'display_name'=>'PUT:/api/v1/rides']);
        $rides_post = \Solunes\Master\App\Permission::create(['name'=>'Carreras post', 'display_name'=>'POST:/api/v1/rides']);
        

        $request_get = \Solunes\Master\App\Permission::create(['name'=>'Request Get', 'display_name'=>'GET:/api/v1/request']);
        $request_post = \Solunes\Master\App\Permission::create(['name'=>'Request Post', 'display_name'=>'POST:/api/v1/request']);
        $request_put = \Solunes\Master\App\Permission::create(['name'=>'Request Put', 'display_name'=>'PUT:/api/v1/request']);
        $request_delete = \Solunes\Master\App\Permission::create(['name'=>'Request Delete', 'display_name'=>'DELETE:/api/v1/request']);
        $requests_get = \Solunes\Master\App\Permission::create(['name'=>'Requests Get', 'display_name'=>'GET:/api/v1/requests']);
        $requests_put = \Solunes\Master\App\Permission::create(['name'=>'Requests Put', 'display_name'=>'PUT:/api/v1/requests']);
        $driver_requests_get = \Solunes\Master\App\Permission::create(['name'=>'Driver Requests', 'display_name'=>'GET:/api/v1/driver-requests']);

        $payment_methods_get = \Solunes\Master\App\Permission::create(['name'=>'Metodo de Pago Get', 'display_name'=>'GET:/api/v1/payment-methods']);

        $payment_configuration_distance_get = \Solunes\Master\App\Permission::create(['name'=>'Prioridad por distancia y tiempo GET', 'display_name'=>'GET:/api/v1/configuration-distance']);
        $payment_configuration_distance_post = \Solunes\Master\App\Permission::create(['name'=>'Prioridad por distancia y tiempo POST', 'display_name'=>'POST:/api/v1/configuration-distance']);
        $payment_configuration_distance_put = \Solunes\Master\App\Permission::create(['name'=>'Prioridad por distancia y tiempo PUT', 'display_name'=>'PUT:/api/v1/configuration-distance']);
        $payment_configuration_distance_delete = \Solunes\Master\App\Permission::create(['name'=>'Prioridad por distancia y tiempo DELETE', 'display_name'=>'DELETE:/api/v1/configuration-distance']);


        // ASOCIACION DE ROL Y PERMISO
        $admin->permission_role()->attach([$parameters_perm->id, $customers_perm->id]);
        $member->permission_role()->attach([$members_perm->id]);
        $subadmin->permission_role()->attach([$subadmin_perm->id, $customers_perm->id, $sales_perm->id]);
        //$new_role->permission_role()->attach([$new_permission->id, $new_permission1->id]); // MUESTRA PARA ASOCIAR PERMISO A ROL
        
        // PASSENGER PERMISSIONS
        $passanger->permission_role()->attach([
            $user_post->id, 
            $user_get->id, 
            $user_delete->id,
            $user_patch->id,
            $user_put->id,
            $organization_get->id,
            $type_request_post->id,
            $type_request_get->id,
            $driver_get->id,
            $driver_rating_post->id,
            $ride_report_post->id,
            $ride_report_get->id,
            $rates_post->id,
            $support_question_get->id,

            $rides_put->id,
            $ride_put->id,
            $ride_patch->id,
            $ride_get->id,
            $ride_post->id,
            $rides_post->id,
            $request_get->id,
            $request_post->id,
            $request_put->id,
            $request_delete->id,
            $requests_get->id,
            $requests_put->id,
            $driver_request_post->id,
            $driver_request_get->id,
            $driver_request_delete->id,
            $driver_request_patch->id,
            $driver_request_put->id,
            $payment_methods_get->id
        ]);
        // DRIVER PERMISSION
        $driver->permission_role()->attach([
            $user_post->id, 
            $user_get->id,
            $user_patch->id,
            $user_put->id,

            $organization_get->id,
            $type_request_post->id,
            $type_request_get->id,

            $driver_post->id,
            $driver_get->id,
            $driver_patch->id,
            $driver_put->id,
            $driver_delete->id,

            $user_rating_post->id,

            $ride_report_post->id,
            $ride_report_get->id,

            $rates_post->id,

            $support_question_get->id,

            $rides_put->id,
            $rides_post->id,
            $ride_put->id,
            $ride_patch->id,
            $ride_get->id,
            $ride_post->id,
            $request_get->id,
            $request_post->id,
            $request_put->id,
            $request_delete->id,
            $requests_get->id,
            $requests_put->id,
            $driver_request_post->id,
            $driver_request_get->id,
            $driver_request_delete->id,
            $driver_request_patch->id,
            $driver_request_put->id,
            $payment_methods_get->id,
            $driver_requests_get->id,

            $payment_configuration_distance_post->id,
            $payment_configuration_distance_get->id,
            $payment_configuration_distance_put->id,
            $payment_configuration_distance_delete->id,
        ]);

        // Variables
        \Solunes\Master\App\Variable::create([
            'name' => 'admin_email',
            'type' => 'string',
            'es' => ['value'=>'edumejia30@gmail.com'],
        ]);
        \Solunes\Master\App\Variable::create([
            'name' => 'footer_name',
            'type' => 'string',
            'es' => ['value'=>'Taxis'],
        ]);
        \Solunes\Master\App\Variable::create([
            'name' => 'footer_rights',
            'type' => 'string',
            'es' => ['value'=>'Todos los derechos reservados.'],
        ]);

        // Social Networks
        \App\SocialNetwork::create([
            'code' => 'facebook',
            'url' => 'https://www.facebook.com/',
        ]);
        \App\SocialNetwork::create([
            'code' => 'instagram',
            'url' => 'https://www.instagram.com',
        ]);
        \App\SocialNetwork::create([
            'code' => 'twitter',
            'url' => 'https://www.twitter.com',
        ]);
        \App\SocialNetwork::create([
            'code' => 'youtube',
            'url' => 'https://www.youtube.com',
        ]);
    }
}
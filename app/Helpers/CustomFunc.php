<?php

namespace App\Helpers;

use Form;

class CustomFunc
{

    public static function get_custom_node_array($array, $page, $admin = false)
    {
        $subarray = [];
        switch ($page->customized_name) {
            case 'home':
                /*$subarray['top_title'] = \App\Title::getCode('toptitle')->name;
                $subarray['titleh'] = \App\Title::getCode('titleh')->name;
                $subarray['subtitle'] = \App\Title::getCode('subtitle')->name;
                $subarray['text'] = \App\Title::getCode('text')->name;*/
                //$subarray['content_concepto'] = \App\Content::getCode('concepto')->content;
                $subarray['categories'] = \Solunes\Business\App\Category::has('product_bridges')->with('product_bridges')->limit(4)->get();
                // Registrar Visita e IP
                /*$visit = new \App\Visit;
                $visit->name = 'home';
                $visit->ip = request()->ip();
                $visit->save();*/
                $subarray['themes'] = [];
                /*$subarray['foods'] = \App\Brand::where('product_type', 'foods')->get();
                $subarray['desks'] = \App\Brand::where('product_type', 'desk')->get();*/
                $subarray['brands'] = \Solunes\Business\App\Brand::get();
                //$subarray['values'] = \App\SiteValue::get();
                //$subarray['blogs'] = \App\Blog::limit(3)->orderBy('id','DESC')->get();
                // $subarray['destacados'] = \Solunes\Business\App\ProductBridge::limit(6)->orderBy('id','DESC')->get();
                $subarray['destacados'] = [];
                //$subarray['galleries'] = \Solunes\Product\App\ProductImage::orderBy(\DB::raw('RAND()'))->get();
                //$subarray['galleries'] = \App\SiteGallery::orderBy('order', 'ASC')->where('active', 1)->get();
                break;
            case 'about':
                $subarray['nosotros'] = \App\Content::getCode('nosotros')->content;
                break;
            case 'shop':
                $subarray['categories'] = \Solunes\Business\App\Category::has('product_bridges')->with('product_bridges')->get();
                $subarray['brands'] = \Solunes\Business\App\Brand::has('product_bridges')->orderBy('name')->get();
                $subarray['destacados'] = \Solunes\Business\App\ProductBridge::where('image', 0)->where('active', 1)->get();
                break;
            case 'contact':
                $subarray['contacts'] = \App\Contact::get();
                $subarray['socials'] = \App\SocialNetwork::get();
                break;
        }
        $array['nodes'] = $subarray;
        return $array;
    }

    public static function get_node_array($array, $page, $admin = false)
    {
        $subarray = [];
        $array_node_names = [];
        $array_nodes = [];
        switch ($page->customized_name) {
            case 'contact':
                $array_nodes = ['contact-form'];
                break;
        }
        $subarray = [];
        foreach ($array_nodes as $node_val => $node_name) {
            $node = \Solunes\Master\App\Node::where('name', $node_name)->first();
            $nodes[$node_name] = $node;
            $subarray = \FuncNode::get_items_array($node, $node_val);
            $array['nodes'][$node_name . $node_val] = ['node' => $node, 'subarray' => $subarray];
            if ($node_name == 'contact-form') {
                $array['nodes'][$node_name . $node_val]['subarray']['content'] = \App\Content::getCode('contact-content')->content;
            }
        }
        $array = \CustomFunc::get_scripts_array($array, $nodes);
        return $array;
    }

    public static function get_scripts_array($array, $nodes)
    {
        $script_array = [];
        foreach ($nodes as $node) {
            if ($node->folder == 'form') {
                array_push($script_array, 'form');
                $array['form_array'] = \FuncNode::get_items_array($node);
            } else if (in_array($node->name, ['photo', 'video', 'participant'])) {
                //array_push($script_array, 'lightbox');
                array_push($script_array, 'masonry');
            } else if (in_array($node->name, ['member', 'publication'])) {
                array_push($script_array, 'masonry');
            } else if (in_array($node->name, ['project', 'contact'])) {
                array_push($script_array, 'map');
                array_push($script_array, 'locations-' . $node->name);
                $array['location_array'] = \FuncNode::get_items_array($node);
                if ($node->name == 'project') {
                    array_push($script_array, 'owl-project');
                }
            } else if (in_array($node->name, ['banner'])) {
                array_push($script_array, 'banner');
            }
            $array['script_array'] = $script_array;
        }
        return $array;
    }

    public static function before_migrate_actions()
    {
        // Acciones
    }

    public static function after_migrate_actions()
    {
        // Acciones
    }

    public static function before_seed_actions()
    {
        return 'Before seed realizado correctamente.';
    }

    public static function after_seed_actions()
    {
        $nodes = \Solunes\Master\App\Node::whereIn('name', ['customer-payment', 'single-payment'])->get();
        foreach ($nodes as $node) {
            $node->permission = NULL;
            $node->save();
        }
        return 'After seed realizado correctamente.';
    }

    public static function after_login($user, $last_session, $redirect)
    {
        if ($user->hasRole('admin')) {
            session()->set('url.intended', url('customer-admin/model-list/user'));
        } elseif ($user->hasRole('subadmin')) {
            session()->set('url.intended', url('customer-admin/model-list/driver'));
        } elseif ($user->hasRole('driver')) {
            session()->set('url.intended', url('customer-admin/model-list/request'));
        } elseif ($user->hasRole('passenger')) {
            session()->set('url.intended', url('customer-admin/model-list/request'));
        } elseif ($user->hasRole('alcaldia') || $user->hasRole('sindicato') || $user->hasRole('empresa')) {
            session()->set('url.intended', url('customer-admin/model-list/organization'));
        }
        session()->set('message_success', 'Su sesión fue iniciada correctamente.');
    }

    public static function get_options_relation($submodel, $field, $subnode, $id)
    {
        if ($field->relation_cond == 'nfc_filter') {
            $model = \FuncNode::node_check_model($field->parent);
            if ($item = $model::find($id)) {
                $submodel = $submodel->where('school_id', $item->school_id)->where(function ($q) use ($item) {
                    return $q->whereNull('school_registration_id')->orWhere('id', $item->nfc_id);
                });
            }
        } elseif ($field->relation_cond == 'model_by_brand') {
          
            $model = \FuncNode::node_check_model($field->parent);
            if ($item = $model::find($id)) {
                $submodel = $submodel->where('vehicle_brand_id', $item->vehicle_brand_id);
            }
        } elseif ($field->relation_cond == 'user-belongs-to') {
            $submodel = \App\User::whereHas('role_user', function ($query) {
                $query = $query->where('role_id', 4)->orWhere('role_id', 1);
            });
        }
        return $submodel;
    }

    public static function custom_admin_node_actions($node, $action_nodes)
    {
        if (auth()->user()->hasRole('admin')) {
            if (in_array($node->name, ['user', 'organization', 'driver', 'sindicato', 'driver-vehicle', 'panic-button', 'driver-rating', 'driver-activation', 'firebase-notification', 'vehicle-brand', 'vehicle-model', 'city', 'region'])) {
                $action_nodes = ['excel', 'create'];
            }
        } elseif (auth()->user()->hasRole('alcaldia')) {
            if (in_array($node->name, ['user', 'organization', 'driver', 'sindicato', 'driver-vehicle'])) {
                $action_nodes = ['excel'];
            }
        } elseif (auth()->user()->hasRole('sindicato')) {
            if (in_array($node->name, ['user', 'organization', 'driver', 'sindicato', 'driver-vehicle'])) {
                $action_nodes = ['excel'];
            }
        } elseif (auth()->user()->hasRole('empresa')) {
            if (in_array($node->name, ['user', 'organization', 'driver', 'sindicato', 'driver-vehicle', 'panic-button', 'driver-rating', 'driver-activation'])) {
                $action_nodes = ['excel'];
            }
        }
        if (auth()->user()->hasRole('subadmin')) {
            if (in_array($node->name, ['driver',  'driver-vehicle',  'vehicle-brand', 'vehicle-model'])) {
                $action_nodes = ['excel', 'create'];
            }
        } elseif (auth()->user()->hasRole('alcaldia')) {
            if (in_array($node->name, ['user', 'organization', 'driver', 'sindicato', 'driver-vehicle'])) {
                $action_nodes = ['excel'];
            }
        } elseif (auth()->user()->hasRole('sindicato')) {
            if (in_array($node->name, ['user', 'organization', 'driver', 'sindicato', 'driver-vehicle'])) {
                $action_nodes = ['excel'];
            }
        } elseif (auth()->user()->hasRole('empresa')) {
            if (in_array($node->name, ['user', 'organization', 'driver', 'sindicato', 'driver-vehicle', 'panic-button', 'driver-rating', 'driver-activation'])) {
                $action_nodes = ['excel'];
            }
        }

        return $action_nodes;
    }

    public static function custom_admin_field_actions($node_name, $fields, $action_fields)
    {
        if (auth()->user()->hasRole('admin')) {
            if (in_array($node_name, [
                'user',
                'request',
                'sindicato',
                'driver-vehicle',
                'driver-rating',
                'user-rating',
                'driver-activation',
                'request-waypoint',
                'request-trip',
                'panic-button',
                'firebase-notification'
            ])) {
                $action_fields = ['edit', 'view'];
            }

            if (in_array($node_name, ['driver-vehicle',  'vehicle-brand', 'vehicle-model', 'city', 'region'])) {
                $action_fields = ['edit', 'view', 'delete'];
            }

            if (in_array($node_name, ['driver'])) {
                $action_fields = ['date_exp', 'assign_me', 'marketin', 'actions', 'verified', 'delete_driver'];
            }

            if (in_array($node_name, ['organization'])) {
                $action_fields = ['edit', 'view'];
            }

            if (in_array($node_name, ['user'])) {
                if (request('passenger') == 'true') $action_fields = ['edit-custom'];
            }
        } elseif (auth()->user()->hasRole('alcaldia')) {
            if (in_array($node_name, [
                'user',
                'organization',
                'driver',
                'request',
                'sindicato',
                'driver-vehicle',
                'driver-rating',
                'user-rating',
                'driver-activation',
                'request-waypoint',
                'request-trip',
                'panic-button'
            ])) {
                $action_fields = ['view'];
            }
        } elseif (auth()->user()->hasRole('sindicato')) {
            if (in_array($node_name, [
                'user',
                'organization',
                'driver',
                'request',
                'sindicato',
                'driver-vehicle',
                'driver-rating',
                'user-rating',
                'driver-activation',
                'request-waypoint',
                'request-trip',
                'panic-button'
            ])) {
                $action_fields = ['view'];
            }
        } elseif (auth()->user()->hasRole('empresa')) {
            if (in_array($node_name, ['request', 'driver-rating',  'user-rating', 'driver-activation', 'request-waypoint', 'request-trip'])) {
                $action_fields = ['view'];
            }
            if (in_array($node_name, ['organization', 'driver',  'panic-button'])) {
                $action_fields = ['view', 'edit', 'delete'];
            }
        } elseif (auth()->user()->hasRole('subadmin')) {
            if (in_array($node_name, ['driver'])) {
                $action_fields = ['date_exp', 'assign_me', 'marketin', 'actions', 'verified', 'delete_driver'];
            }

            if (in_array($node_name, ['driver-vehicle',  'vehicle-brand', 'vehicle-model'])) {
                $action_fields = ['edit', 'view', 'delete'];
            }
        }

        return $action_fields;
    }

    public static function custom_admin_get_list($module, $node, $items, $array)
    {
        if (in_array($node->name, ['driver'])) {
            $items = $items->orderBy('created_at', 'DESC');
        }
        return $items;
    }

    public static function custom_admin_get_item($node, $item, $fields)
    {
        if ($node->name == 'customer' || $node->name == 'customer-dependant') {
            /*if(auth()->check()&&auth()->user()->hasRole('member')){
                $fields = $fields->whereNotIn('name', ['member_code','active','status','customer_dependants','name']);
            }*/
        }
        return $fields;
    }

    public static function custom_get_items($subarray, $node, $node_val)
    {
        $items = $subarray['items'];
        /*if($node->name=='blog'){
            $items = $items->orderBy('created_at', 'DESC');
        }*/
        $subarray['items'] = $items;
        return $subarray;
    }

    public static function get_sitemap_array($lang)
    {
        $array = [];
        /*if($lang=='es'){
            $array['member'] = ['url'=>'miembro/', 'url_id'=>'slug', 'priority'=>'0.7'];
            $array['article'] = ['url'=>'articulo/', 'url_id'=>'slug', 'priority'=>'0.5'];
        }*/
        return $array;
    }

    public static function add_node_array($node, $admin)
    {
        if ($node->name == 'article') {
            $cocab = \App\Article::where('member_id', 1)->orderBy('created_at', 'DESC')->get();
            if (!request()->has('member_id') || request()->input('member_id') != 1) {
                $cocab = $cocab->take(3);
            }
            return ['cocab' => $cocab];
        } else {
            return false;
        }
    }

    public static function check_permission($type, $module, $node, $action, $id = NULL)
    {
        // Type = list, item
        $return = 'none';
        if (!$node) {
            return $return;
        }
        if ($node->name == 'temp-file') {
            if ($type == 'list') {
                $return = 'true';
            }
        } else if ($node->name == 'school-registration') {
            if ($type == 'list') {
                if (auth()->user()->hasRole('subadmin')) {
                    $return = 'true';
                }
            }
        }
        return $return;
    }

    public static function get_action_field_labels($response, $action_field, $langs)
    {
        $response = '';
        if ($action_field == 'manual-pay') {
            $response .= '<td class="restore">Pago Manual</td>';
        } else if ($action_field == 'register-school') {
            $response .= '<td class="restore">Inscribir</td>';
        }
        if ($action_field == 'date_exp') {
            $response .= '<td class="restore">Vigencia Licencia</td>';
        }
        if ($action_field == 'verified') {
            $response .= '<td class="restore">Habilitar o Bloquear</td>';
        }
        if ($action_field == 'assign_me') {
            $response .= '<td class="restore">Asignar Conductor</td>';
        }
        if ($action_field == 'marketin') {
            $response .= '<td class="restore">Marketing y Legal</td>';
        }
        // if($action_field=='legal'){
        //     $response .= '<td class="restore">Legal</td>';
        // }
        // if($action_field=='send-email-libelula'){
        //     $response .= '<td class="restore">Email Libelula</td>';
        // }
        if ($action_field == 'actions') {
            $response .= '<td class="restore">Acciones</td>';
        }
        if ($action_field == 'delete_driver') {
            $response .= '<td class="restore">Eliminar</td>';
        }
        if (request('passenger') == 'true') {
            if ($action_field == 'edit-custom') {
                $response .= '<td class="restore">Editar</td>';
            }
        }


        return $response;
    }

    public static function get_action_field_values($response, $module, $model, $item, $action_field, $langs)
    {

        $response = '';
        if ($action_field == 'manual-pay') {
            if ($item->status == 'pending') {
                $confirm_message = "'¿Está seguro que desea registrar este item como pagado?'";
                $response .= '<td class="ineditable restore"><a onclick="return confirm(' . $confirm_message . ')" href="' . url('admin/manual-pay/' . $item->id) . '">Registrar Pago Manual</a></td>';
            } else {
                $response .= '<td class="ineditable restore"><a href="#">Ya Pagado</a></td>';
            }
        } else if ($action_field == 'register-school') {
            if ($item->active_school_registration) {
                $response .= '<td class="ineditable restore"><a href="' . url('admin/model/school-registration/edit/' . $item->active_school_registration->id . '/es') . '">Ya Insrito</a></td>';
            } else {
                $response .= '<td class="ineditable restore"><a href="' . url('admin/model/school-registration/create?customer_id=' . $item->parent_id . '&customer_dependant_id=' . $item->id) . '">Inscribir</a></td>';
            }
        } else if ($action_field == 'date_exp') {

            $now = \Carbon\Carbon::now();
            $expirationDate = \Carbon\Carbon::parse($item->license_expiration_date);
            $diffInDays = $now->diffInDays($expirationDate);

            if (floatval($diffInDays) <= 15) {
                $response .= '<td class="text-danger">' . $diffInDays . ' Dias</td>';
            } else {
                $response .= '<td class="text-success">' . $diffInDays . ' Dias</td>';
            }
        } elseif ($action_field == 'actions') {
            if (auth()->user()->hasRole('admin')) {
                $driverVehicles = \App\DriverVehicle::where('parent_id', $item->id)->first();
                if ($driverVehicles) {
                    $response .= '<td class="edit">
                    <a style="display: inline !important;" href="/customer-admin/model/driver/view/' . $item->id . '">Ver</a>
                    <a style="display: inline !important;" href="/customer-admin/model/driver/edit/' . $item->id . '"> Editar </a>
                    <a style="display: inline !important; background: #7367f0; color: white; padding: 3px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;" href="/customer-admin/driver-detail/' . $item->id . '">&#128269; Revisar Solicitud</a>
                    </td>';
                } else {
                    $response .= '<td class="edit" >
                    <a style="display: inline !important;" href="/customer-admin/model/driver/view/' . $item->id . '">Ver</a>
                    <br>
                    <a style="display: inline !important;" href="/customer-admin/model/driver/edit/' . $item->id . '">Editar</a>
                    <br>
                    <br>
                    <a  style="display: inline !important;" href="/customer-admin/register-driver/step1-update/' . $item->id . '" target="_blank">Reg. Vehiculo</a>
                    <br>
                    <a style="display: inline !important; background: #7367f0; color: white; padding: 3px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;" href="/customer-admin/driver-detail/' . $item->id . '">&#128269; Revisar Solicitud</a>
                    </td>';
                }
            } elseif (auth()->user()->hasRole('subadmin')) {
                $driverVehicles = \App\DriverVehicle::where('parent_id', $item->id)->first();
                // if($driverVehicles){
                //     $response .= '<td class="ineditable edit">
                //     <a style="display: inline !important;" href="/customer-admin/model/driver/view/'.$item->id.'">Ver</a>
                //     </td>';
                // }else{
                //     $response .= '<td class="ineditable edit" >
                //     <a style="display: inline !important;" href="/customer-admin/model/driver/view/'.$item->id.'">Ver</a>
                //     <a  style="display: inline !important;" href="/customer-admin/register-driver/step1-update/'.$item->id.'" target="_blank">Reg. Vehiculo</a>
                //     </td>';
                // }
                if ($driverVehicles) {


                    $response .= '<td class="edit">
                    <a style="display: inline !important;" href="/customer-admin/model/driver/view/' . $item->id . '">Ver</a>
                    <a style="display: inline !important;" href="/customer-admin/model/driver/edit/' . $item->id . '"> Editar </a>
                    </td>';
                } else {
                    // ineditable
                    $response .= '<td class=" edit" >
                    <a style="display: inline !important;" href="/customer-admin/model/driver/view/' . $item->id . '">Ver</a>
                    <a style="display: inline !important;" href="/customer-admin/model/driver/edit/' . $item->id . '">Editar</a>
                    <br>
                    <br>
                    <a  style="display: inline !important;" href="/customer-admin/register-driver/step1-update/' . $item->id . '" target="_blank">Reg. Vehiculo</a>
                    </td>';
                }
            }
        } else if ($action_field == 'verified') {
            $user = \App\User::where('id', $item->user_id)->first();
            if (!$user) {
                $response .= '<td class="text-danger">user no register</td>';
            } else {
                if (!$user->is_verify) {
                    $response .= '<td class="ineditable restore" ><a href="/customer-admin/verify/' . $item->user_id . '">Aprobar Registro</a></td>';
                } else {
                    $response .= '<td class="ineditable delete" ><a href="/customer-admin/verify/' . $item->user_id . '">Bloquear</a></td>';
                }
            }
        } else if ($action_field == 'delete_driver') {
            $response .= '<td class="ineditable delete" ><a href="/customer-admin/delete-driver/' . $item->id . '">Eliminar</a></td>';
        }
        if (request('passenger') == 'true') {
            if ($action_field == 'edit-custom') {
                $response .= '<td class="ineditable restore" ><a href="/customer-admin/update-passenger/' . $item->id . '">Editar</a></td>';
            }
        }

        if ($action_field == 'marketin') {
            $response .= '<td class="ineditable">';
            if ($item->marketing_check) {
                $response .= '
                <div style="display: flex; justify-content: space-between;">
                    <label >Marketing</label>
                    <input type="checkbox" checked onclick="marketingChange(' . $item->id . ', \'marketing_check\')"" /> 
                </div>';
            } else {
                $response .= '
                <div style="display: flex; justify-content: space-between;">
                    <label>Marketing</label>
                    <input type="checkbox" onclick="marketingChange(' . $item->id . ', \'marketing_check\')" />
                </div>';
            }
            if ($item->legal_check) {
                $response .= '<div style="display: flex;  justify-content: space-between;">
                        <label>Legal</label>
                        <input type="checkbox" checked  onclick="marketingChange(' . $item->id . ', \'legal_check\')" " />
                    </div>';
            } else {
                $response .= '
                <div style="display: flex;  justify-content: space-between;">
                    <label>Legal</label>
                    <input type="checkbox" onclick="marketingChange(' . $item->id . ', \'legal_check\')" />
                </div>';
            }

            if ($item->send_email_libelula) {
                $response .= '<div style="display: flex;  justify-content: space-between;">
                    <label>Email Libélula</label>
                    <input type="checkbox" checked  disabled />
                </div>';
            } else {
                $response .= '
                <div style="display: flex; justify-content: space-between;">
                    <label>Email Libélula</label>
                    <input type="checkbox" onclick="sendEmailLibelula(' . $item->id . ')" />
                </div>';
            }
            $response .= '</td>';
        } elseif ($action_field == 'assign_me') {
            $response .= '<td class="ineditable">';

            if (auth()->user()->hasRole('admin')) {
                if ($item->user_belongs_to_id != null) {
                    $user = \App\User::where('id', $item->user_belongs_to_id)->first();
                    $response .= $user->first_name . ' ' .  $user->last_name;
                } else {
                    $response .= 'Sin asignar';
                }
            } elseif (auth()->user()->hasRole('subadmin')) {
                if ($item->user_belongs_to_id != null) {
                    $response .= '<div style="display: flex;  justify-content: space-between;">
                        <label>Asignarme Conductor</label>
                        <input type="checkbox" checked onclick="assingMeDriver(' . $item->id . ')"   />
                    </div>';
                } else {
                    $response .= '
                    <div style="display: flex; justify-content: space-between;">
                            <label>Asignarme Conductor</label></label>
                        <input type="checkbox" onclick="assingMeDriver(' . $item->id . ')" />
                    </div>';
                }
            }


            $response .= '</td>';
        }

        // if($action_field == 'send-email-libelula'){
        //     if($item->send_email_libelula){
        //         $response .= '<td class="ineditable" >
        //             <input type="checkbox" checked  disabled />
        //         </td>';
        //     }else{
        //         $response .= '<td class="ineditable" ><input type="checkbox" onclick="sendEmailLibelula('.$item->id.')" /></td>';   
        //     }       
        // }

        return $response;
    }

    public static function custom_node_request($node, $items, $action, $req_value)
    {
        if ($action == 'orderCocabFirst') {
            $items = $items->orderByRaw(\DB::raw("FIELD(member_id, '1') DESC"));
        }
        return $items;
    }

    public static function customer_successful_payment($payment)
    {
        $sale_payment = $payment->sale_payment;
        $sale = $payment->sale_payment->sale;
        $sale_delivery = $sale->sale_deliveries()->first();
        if ($sale_payment->payment_method->code == 'pagostt' && $sale_delivery) {
            $link = \CustomFunc::sendSuccessMail($sale);
            $response_1 = $link;
            $email = \Solunes\Master\App\Variable::where('name', 'admin_email')->first();
            $email = $email->value;
            \Mail::send('emails.notifications.daily-report', [], function ($message) use ($response_1, $email) {
                //Test
                //$send_array = ['edumejia30@gmail.com'];
                // Samsung
                $send_array = [$email];
                $message->to($send_array)->subject('Compra recibida en La Ganga - ' . date('Y-m-d'));
                $message->replyTo([$email]);
                $message->attach($response_1);
            });
        }

        $vars = ['@venta@' => $sale->name, '@total@' => $sale->amount, '@url@' => url('')];
        \FuncNode::make_email('successful_payment', [$sale->customer->email], $vars);

        return true;
    }

    public static function sendSuccessMail($sale)
    {
        $date = date('Y-m-d');
        $dir = public_path('excel');
        $file = \Excel::create('successful-sale-' . $sale->id, function ($excel) use ($sale, $date) {
            $excel->sheet('Resumen-de-Compra', function ($sheet) use ($sale, $date) {
                $sale_delivery = $sale->sale_deliveries()->first();
                if ($sale_delivery && $sale_delivery->shipping_id == 1) {
                    $delivery_store = 'Si';
                } else {
                    $delivery_store = 'No';
                }
                $sheet->row(1, ['Número de Pedido Web', $sale->id]);
                $sheet->row(2, ['Fecha', $date]);
                $sheet->row(3, ['Nombre Cliente', $sale->customer->name]);
                $sheet->row(4, ['Celular', $sale_delivery->phone]);
                $sheet->row(5, ['Nombre a Facturar', $sale->invoice_name]);
                $sheet->row(6, ['NIT Factura', $sale->invoice_nit]);
                if ($sale_delivery) {
                    $sheet->row(7, ['Dirección de Entrega', $sale_delivery->address . ' - ' . $sale_delivery->address_extra]);
                    $sheet->row(8, ['Link Dirección Google Map', 'https://www.google.com/maps/search/?api=1&query=' . $sale_delivery->latitude . ',' . $sale_delivery->longitude]);
                    $sheet->row(9, ['Tipo de Pago', 'Libélula']);
                    $sheet->row(10, ['Ciudad', $sale_delivery->city->name]);
                } else {
                    $sheet->row(7, ['Dirección de Entrega', 'Indefinido']);
                    $sheet->row(8, ['Link Dirección Google Map', 'Indefinido']);
                    $sheet->row(9, ['Tipo de Pago', 'Pago en Caja']);
                    $sheet->row(10, ['Ciudad', 'Indefinido']);
                }
                $sheet->row(11, ['Tipo de Envío', $sale_delivery->shipping->name]);
                $sheet->row(12, ['Regalo', $sale->gift ? 'Si' : 'No']);
                $sheet->row(13, ['Nombre de la persona a la que se le va enviar el regalo', $sale->gift_name]);
                $sheet->row(14, ['Celular de la persona a la que se le va enviar el regalo', $sale->gift_cellphone]);
                $sheet->row(15, ['Nota del regalo', $sale->gift_message]);
                $discount_xls = 0;
                if ($sale->coupon_code && $sale->order_amount != $sale->paid_amount) {
                    $discount_xls = $sale->paid_amount - $sale->order_amount;
                }

                $row = 17;
                $sheet->row($row, function ($row) {
                    $row->setFontWeight('bold');
                });
                $col_array[] = 'Linea';
                $col_array[] = 'Nombre de Producto';
                $col_array[] = 'Cantidad';
                $col_array[] = 'Precio Unit';
                $col_array[] = 'Precio con descuento';
                $col_array[] = 'Total';
                $col_array[] = 'Total General';
                $sheet->row($row, $col_array);
                $sheet->row($row, function ($row) {
                    $row->setFontWeight('bold');
                });
                $total = 0;
                foreach ($sale->sale_items as $sale_item) {
                    $product_bridge = $sale_item->product_bridge;
                    $product = $product_bridge->product;
                    $col_array = [
                        $product->category->name,
                        $product->barcode . ' | ' . $product->name,
                        $sale_item->quantity,
                        $sale_item->price,
                        $sale_item->product_bridge->real_price,
                        round($sale_item->product_bridge->real_price * $sale_item->quantity, 2),
                        round($sale_item->product_bridge->real_price * $sale_item->quantity, 2)
                    ];
                    $row++;
                    $total += round($sale_item->product_bridge->real_price * $sale_item->quantity, 2);
                    $sheet->row($row, $col_array);
                }
                $row++;

                $sheet->row($row + 1, ['TOTAL']);
                $sheet->row($row + 1, function ($row) {
                    $row->setFontWeight('bold');
                });
                $sheet->row($row + 2, ['Descuento(general/cupón)', ' Bs. ' . $discount_xls]);
                $sheet->row($row + 3, ['Monto final total', ' Bs. ' . ($total - $discount_xls)]);
                /*$col_array = [$row, 'TOTAL', $total];
            $sheet->row($count, $col_array);
            $sheet->row($count, function($row) {
              $row->setFontWeight('bold');
            });*/
            });
        })->store('xlsx', $dir, true);
        return $file['full'];
        return response()->download($file['full']);
    }

    public static function vardump()
    {
        $arg_list = func_get_args();
        foreach ($arg_list as $variable) {
            echo '<pre style="color: #000; background-color: #fff;">';
            echo htmlspecialchars(var_export($variable, true));
            echo '</pre>';
        }
    }

    public static function custom_filter($array, $items, $appends, $field_name, $custom_data)
    {
        if ($custom_data == 'project_member') {
            $array['filter_options'][$field_name] = ['any' => trans('admin.any')] + \App\Member::where('type', 'member')->orderBy('name', 'ASC')->lists('name', 'id')->toArray();
            $custom_value = 'any';
            if (request()->input('f_' . $field_name)) {
                $custom_value = request()->input('f_' . $field_name);
            }
            if ($custom_value != 'any') {
                $appends['f_' . $field_name] = $custom_value;
                $items = $items->where($field_name, $custom_value);
            }
        }
        return ['array' => $array, 'appends' => $appends, 'items' => $items];
    }

    public static function calculate_shipping_cost($shipping, $country_id, $city_id, $weight, $map_coordinates, $agency)
    {

        if (config('sales.delivery_select_hour')) {
            $shipping_times = $shipping->shipping_times()->lists('id', 'name')->toArray();
        }
        // if(config('sales.delivery_select_day')){
        //     $shipping_dates = \Sales::getShippingDates($shipping, $shipping->shipping_city->shipping_days);
        // }
        if ($agency && $agency->map) {
            $agency_coordinates = explode(';', $agency->map);
            $latitudeFrom = $agency_coordinates[0];
            $longitudeFrom = $agency_coordinates[1];
        } else {
            // $latitudeFrom = NULL;
            // $longitudeFrom = NULL;
        }
        $latitudeFrom = -17.7729835;
        $longitudeFrom = -63.1883167;
        if ($map_coordinates) {
            $map_coordinates = explode(';', $map_coordinates);
            $latitudeTo = $map_coordinates[0];
            $longitudeTo = $map_coordinates[1];
        } else {
            $latitudeTo = NULL;
            $longitudeTo = NULL;
        }
        $distance = NULL;
        if ($latitudeFrom && $longitudeFrom && $latitudeTo && $longitudeTo) {
            $url = 'https://maps.googleapis.com';
            $action = 'maps/api/distancematrix/json';
            $parameters['origins'] = $latitudeFrom . ',' . $longitudeFrom;
            $parameters['destinations'] = $latitudeTo . ',' . $longitudeTo;
            $parameters['key'] = config('solunes.google_maps_key');
            $distance = \External::guzzleGet($url, $action, $parameters);
            if (count($distance['rows']) > 0) {
                $element = $distance['rows'][0]['elements'][0];
                $distance = $element['distance']['value'];
                $duration = $element['duration']['value'];
                \Log::info('distanceGoogle: ' . $agency->name . ' - ' . $distance);
            } else {
                $earthRadius = 6371000;
                // convert from degrees to radians
                $latFrom = deg2rad($latitudeFrom);
                $lonFrom = deg2rad($longitudeFrom);
                $latTo = deg2rad($latitudeTo);
                $lonTo = deg2rad($longitudeTo);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
                $distance = $angle * $earthRadius;
                \Log::info('distanceManual: ' . $distance);
            }
        }
        $shipping_cost = NULL;
        if ($shipping->id == 3) {
            // if($distance>16000){
            //     $shipping = false;
            // } else if($distance>13000){
            //     $shipping_cost = 50;
            // } else if($distance>10000){
            //     $shipping_cost = 40;
            // } else if($distance>8500){
            //     $shipping_cost = 35;
            // } else if($distance>7500){
            //     $shipping_cost = 30;
            // } else if($distance>6500){
            //     $shipping_cost = 25;
            // } else if($distance>5000){
            //     $shipping_cost = 20;
            // } else if($distance>3000){
            //     $shipping_cost = 18;
            // } else {
            //     $shipping_cost = 15;
            // }
            if ($distance > 15000) {
                $shipping = false;
            } else if ($distance > 10000) {
                $shipping_cost = 35;
            } else if ($distance > 5000) {
                $shipping_cost = 25;
            } else if ($distance > 3000) {
                $shipping_cost = 20;
            } else {
                $shipping_cost = 15;
            }
        } else {
            // if($distance>16000){
            //     $shipping = false;
            // } else if($distance>13000){
            //     $shipping_cost = 40;
            // } else if($distance>10000){
            //     $shipping_cost = 35;
            // } else if($distance>8500){
            //     $shipping_cost = 30;
            // } else if($distance>7500){
            //     $shipping_cost = 25;
            // } else if($distance>6500){
            //     $shipping_cost = 20;
            // } else if($distance>5000){
            //     $shipping_cost = 18;
            // } else if($distance>3000){
            //     $shipping_cost = 15;
            // } else {
            //     $shipping_cost = 15;
            // }
            if ($distance > 15000) {
                $shipping = false;
            } else if ($distance > 10000) {
                $shipping_cost = 35;
            } else if ($distance > 5000) {
                $shipping_cost = 25;
            } else if ($distance > 3000) {
                $shipping_cost = 20;
            } else {
                $shipping_cost = 15;
            }
        }
        if (!$shipping) {
            $shipping_dates = [];
            $shipping_times = [];
        }
        $array = ['shipping' => $shipping, 'shipping_cities' => [], 'shipping_city' => $city_id, 'other_city' => NULL, 'shipping_cost' => $shipping_cost, 'shipping_time' => 1, 'shipping_dates' => [], 'shipping_times' => [], 'distance' => $distance];
        // $array = ['shipping'=>true, 'shipping_cities'=>$shipping_cities_array, 'shipping_city'=>$shipping_city->city_id, 'other_city'=>$other_city, 'shipping_cost'=>$shipping_cost, 'shipping_time'=>$shipping_time, 'shipping_dates'=>$shipping_dates, 'shipping_times'=>$shipping_times];
        return $array;
    }

    public static function customer_dashboard_custom_filters($module, $node, $items)
    {
        $user = auth()->user();
        if ($user->hasRole('admin')) {
        } elseif ($user->hasRole('subadmin')) {
            if ($node->name == 'driver') {
                $items = $items->where('user_belongs_to_id', $user->id)->orWhere('user_belongs_to_id', null);
            }
            if ($node->name == 'driver-vehicle') {
                $items = $items->whereHas('driver', function ($query) use ($user) {
                    $query = $query->where('user_belongs_to_id', $user->id);
                });
            }
        } elseif ($user->hasRole('passenger')) {
            if ($node->name == 'request') {
                $items = $items->where('user_id', $user->id);
            }
        } elseif ($user->hasRole('driver')) {
            if ($node->name == 'request') {
                $items = $items->whereHas('request_drivers', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } elseif ($node->name == 'driver-vehicle') {
                $items = $items->whereHas('driver', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        } elseif ($user->hasRole('alcaldia')) {
            if ($node->name == 'organization') {
                $items = $items->where('city_id', $user->city_id);
            }
            if ($node->name == 'driver') {
                $items = $items->where('city_id', $user->city_id);
            }
            if ($node->name == 'sindicato') {
                $items = $items->where('city_id', $user->city_id);
            } elseif ($node->name == 'user') {
                $users = \App\User::get();
                $usersIds = [];
                foreach ($users as $user) {
                    if ($user->hasRole('passenger')) {
                        $usersIds[] = $user->id;
                    }
                    if ($user->hasRole('driver')) {
                        $usersIds[] = $user->id;
                    }
                }
                $items = $items->whereIn('id', $usersIds);
            }
        } elseif ($user->hasRole('sindicato')) {
            if ($node->name == 'organization') {
                $items = $items->where('sindicato_id', $user->sindicato_id);
            } elseif ($node->name == 'driver') {
                $items = $items->whereHas('organization', function ($q) use ($user) {
                    $q->where('sindicato_id', $user->sindicato_id);
                });
            } elseif ($node->name == 'user') {
                $users = \App\User::get();
                $usersIds = [];
                foreach ($users as $user) {
                    if ($user->hasRole('passenger')) {
                        $usersIds[] = $user->id;
                    }
                    if ($user->hasRole('driver')) {
                        $usersIds[] = $user->id;
                    }
                }
                $items = $items->whereIn('id', $usersIds);
            }
        } elseif ($user->hasRole('empresa')) {
            if ($node->name == 'organization') {
                $items = $items->where('id', $user->organization_id);
            } elseif ($node->name == 'driver') {
                $items = $items->whereHas('organization', function ($q) use ($user) {
                    $q->where('id', $user->organization_id);
                });
            } elseif ($node->name == 'driver-vehicle') {
                $items = $items->whereHas('driver', function ($q) use ($user) {
                    $q->where('organization_id', $user->organization_id);
                });
            } elseif ($node->name == 'request') {
                $items = $items->whereHas('taxi_company', function ($q) use ($user) {
                    $q->where('taxi_company', $user->organization_id);
                });
            } elseif ($node->name == 'driver-rating') {
                $items = $items->whereHas('driver', function ($q) use ($user) {
                    $q->where('organization_id', $user->organization_id);
                });
            } elseif ($node->name == 'driver-activation') {
                $items = $items->whereHas('driver', function ($q) use ($user) {
                    $q->where('organization_id', $user->organization_id);
                });
            } elseif ($node->name == 'user') {
                $users = \App\User::get();
                $usersIds = [];
                foreach ($users as $user) {
                    if ($user->hasRole('passenger')) {
                        $usersIds[] = $user->id;
                    }
                    if ($user->hasRole('driver')) {
                        $usersIds[] = $user->id;
                    }
                }
                $items = $items->whereIn('id', $usersIds);
            }
        }
        return $items;
    }

    public static function item_form_add_html_after_form($module, $model, $action, $files, $fields, $i) {
        if ($model == "driver" && $i && $i->id) {
            $driver = $i;
            $user = \App\User::find($driver->user_id);
            $vehicles = \App\DriverVehicle::where('parent_id', $driver->id)->with(['vehicle_brand', 'vehicle_model'])->get();
            $isApproved = $user && $user->is_verify;

            $html = '<div style="margin-top:30px; border:2px solid #7c4dff; border-radius:10px; padding:25px; background:#fafafe;">';

            // Status
            $html .= '<div style="text-align:center; margin-bottom:20px;">';
            $html .= '<h3 style="color:#333;">Estado del Conductor</h3>';
            if ($isApproved) {
                $html .= '<span style="display:inline-block;padding:8px 20px;background:#d4edda;color:#155724;border-radius:20px;font-weight:bold;font-size:16px;">APROBADO</span>';
            } else {
                $html .= '<span style="display:inline-block;padding:8px 20px;background:#fff3cd;color:#856404;border-radius:20px;font-weight:bold;font-size:16px;">PENDIENTE DE APROBACION</span>';
            }
            $html .= '</div>';

            // Images section
            $html .= '<h3 style="color:#7c4dff; border-bottom:2px solid #7c4dff; padding-bottom:8px; margin-bottom:15px;">Documentos e Imagenes del Conductor</h3>';
            $imageFields = [
                ['field' => 'image', 'label' => 'Foto de Perfil', 'folder' => 'driver-image'],
                ['field' => 'license_front_image', 'label' => 'Licencia (Frente)', 'folder' => 'driver-license_front_image'],
                ['field' => 'license_back_image', 'label' => 'Licencia (Reverso)', 'folder' => 'driver-license_back_image'],
                ['field' => 'ci_front_image', 'label' => 'CI (Anverso)', 'folder' => 'driver-ci_front_image'],
                ['field' => 'ci_back_image', 'label' => 'CI (Reverso)', 'folder' => 'driver-ci_back_image'],
            ];
            $html .= '<div style="display:flex; flex-wrap:wrap; gap:15px; margin-bottom:20px;">';
            foreach ($imageFields as $imgField) {
                $fn = $imgField['field'];
                $val = $driver->$fn;
                if ($val && $val !== '0' && $val !== 0 && $val !== false) {
                    $imgUrl = \Storage::url($imgField['folder'] . '/normal/' . $val);
                    $html .= '<div style="text-align:center; background:#fff; padding:10px; border-radius:8px; border:1px solid #ddd; min-width:180px;">';
                    $html .= '<p style="font-weight:bold; margin-bottom:5px; font-size:13px; color:#555;">' . $imgField['label'] . '</p>';
                    $html .= '<a href="' . e($imgUrl) . '" target="_blank"><img src="' . e($imgUrl) . '" style="max-width:200px; max-height:160px; border-radius:6px;" onerror="this.parentElement.innerHTML=\'<span style=color:#ccc>Sin imagen</span>\'"></a>';
                    $html .= '</div>';
                }
            }
            $html .= '</div>';

            // Vehicle images
            if (count($vehicles) > 0) {
                $html .= '<h4 style="color:#7c4dff; margin-top:15px;">Imagenes de Vehiculos</h4>';
                $html .= '<div style="display:flex; flex-wrap:wrap; gap:15px; margin-bottom:20px;">';
                foreach ($vehicles as $v) {
                    $vehImgs = [
                        ['field'=>'vehicle_image', 'label'=>'Vehiculo (' . ($v->number_plate ?: 'S/P') . ')', 'folder'=>'driver-vehicle-vehicle_image'],
                        ['field'=>'side_image', 'label'=>'Lateral (' . ($v->number_plate ?: 'S/P') . ')', 'folder'=>'driver-vehicle-side_image'],
                        ['field'=>'rua_image', 'label'=>'RUAT (' . ($v->number_plate ?: 'S/P') . ')', 'folder'=>'driver-vehicle-rua_image'],
                    ];
                    foreach ($vehImgs as $vImg) {
                        $fn = $vImg['field'];
                        $vv = $v->$fn;
                        if ($vv && $vv !== '0' && $vv !== 0 && $vv !== false) {
                            $vImgUrl = \Storage::url($vImg['folder'] . '/normal/' . $vv);
                            $html .= '<div style="text-align:center; background:#fff; padding:10px; border-radius:8px; border:1px solid #ddd; min-width:180px;">';
                            $html .= '<p style="font-weight:bold; margin-bottom:5px; font-size:13px; color:#555;">' . $vImg['label'] . '</p>';
                            $html .= '<a href="' . e($vImgUrl) . '" target="_blank"><img src="' . e($vImgUrl) . '" style="max-width:200px; max-height:160px; border-radius:6px;" onerror="this.parentElement.innerHTML=\'<span style=color:#ccc>Sin imagen</span>\'"></a>';
                            $html .= '</div>';
                        }
                    }
                }
                $html .= '</div>';
            }

            // Approve/Reject
            $html .= '<div style="text-align:center; margin-top:20px; padding-top:20px; border-top:2px dashed #7c4dff;">';
            $html .= '<h4 style="margin-bottom:15px;">Accion de Aprobacion</h4>';
            if (!$isApproved) {
                $html .= '<p style="color:#666; margin-bottom:15px;">Revise los datos y documentos. Si todo esta correcto, apruebe el registro.</p>';
                $hasVeh = count($vehicles) > 0;
                if (!$hasVeh) {
                    $html .= '<p style="color:red; font-weight:bold; margin-bottom:10px;">No se puede aprobar: debe tener al menos un vehiculo.</p>';
                }
                $ds = !$hasVeh ? 'pointer-events:none; opacity:0.5;' : '';
                $html .= '<a href="/customer-admin/approve-driver/' . $driver->id . '" onclick="return confirm(\'Aprobar a este conductor? Podra usar la app.\')" style="display:inline-block; padding:12px 30px; background:#28a745; color:#fff; border-radius:5px; text-decoration:none; font-weight:bold; font-size:16px; margin-right:10px;' . $ds . '">Aprobar Conductor</a>';
                $html .= '<a href="/customer-admin/reject-driver/' . $driver->id . '" onclick="return confirm(\'Rechazar a este conductor?\')" style="display:inline-block; padding:12px 30px; background:#dc3545; color:#fff; border-radius:5px; text-decoration:none; font-weight:bold; font-size:16px;">Rechazar / Bloquear</a>';
            } else {
                $html .= '<p style="color:#155724; font-size:18px;">Este conductor ya esta <strong>APROBADO</strong>.</p>';
                $html .= '<a href="/customer-admin/reject-driver/' . $driver->id . '" onclick="return confirm(\'Bloquear a este conductor?\')" style="display:inline-block; padding:10px 25px; background:#dc3545; color:#fff; border-radius:5px; text-decoration:none; margin-top:10px;">Bloquear Conductor</a>';
            }
            $html .= '</div>';
            $html .= '</div>';
            return $html;
        }
        return "";
    }

    public static function item_form_add_html_before_button($module, $model, $action, $files, $fields, $i) {
        return "";
    }
}

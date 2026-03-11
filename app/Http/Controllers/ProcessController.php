<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use AdminItem;

class ProcessController extends Controller {

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url) {
	  $this->prev = $url->previous();
	}

  public function getChangeLocale($locale) {
    \Session::put('locale', $locale);
    return redirect($this->prev);
  }

  public function postLogin() {
    return view('content.log');
  }

	public function postModel(Request $request) {
      $model = $request->input('model_node');
      $lang_code = $request->input('lang_code');
      $node = \Solunes\Master\App\Node::where('name', $model)->first();
      //$action = $request->input('action');
      // Medidads de seguridad
      $action = 'send';
      if($node->folder!='form'){
        return redirect($this->prev)->with(['message_error'=>'Hubo un error al procesar el formulario.']);
      }
      $response = AdminItem::post_request($model, $action, $request);
      $item = $response[1];
      $redirect = $this->prev;
      if($response[0]->passes()) {
        $item = AdminItem::post_request_success($request, $model, $item, 'process');
        if($model=='contact-form'){
          $vars = ['@url@'=>url('admin/model-list/contact-form'), '@name@'=>$item->name, '@email@'=>$item->email, '@message@'=>$item->message];
          \FuncNode::make_email('contact_form', [\FuncNode::check_var('admin_email')], $vars);
          return 'MF000';
        }
  		  return redirect($redirect)->with('message_success', trans('ajax.'.$model.'_success'));
  	  } else {
  		  return redirect($redirect)->with(array('message_error' => trans('ajax.'.$model.'_fail')))->withErrors($response[0])->withInput();
  	  }
	}

  public function postFormulario(Request $request) {
    if($request->has('name')&&$request->has('email')){
      $contact = new \App\ContactForm;
      $contact->name = $request->input('name');
      $contact->email = $request->input('email');
      //$contact->subject = $request->input('subject');
      $contact->message = $request->input('message');
      $contact->save();

      $vars = ['@name@'=>$contact->name, '@email@'=>$contact->email,'@message@'=>$contact->message,'@url@'=>url('admin/model/contact-form/edit/'.$contact->id)];
      \FuncNode::make_email('contact_form', [\FuncNode::check_var('admin_email')], $vars);
      //return ['mailSent'=>'true','message'=>'Su formulario fue enviado correctamente.'];
      return redirect($this->prev.'#alert')->with('message_success', 'Su formulario fue enviado correctamente.');
    } else {
      //return ['mailError'=>'false','message'=>'Su formulario no fue enviado.'];
      return redirect($this->prev.'#alert')->with('message_error', 'Su formulario no fue enviado porque debe llenar todos los campos.');
    }
  }

  public function postProductBridgeSearch(Request $request) {
      $products = [];
      if($request->has('term')){
          $product_slug = config('business.product_slug');
          $term = $request->input('term');
          $brands_ids = \Solunes\Business\App\Brand::where('name', 'LIKE', '%'.$term.'%')->lists('id')->toArray();
          $product_bridge_items = \Solunes\Product\App\ProductTranslation::where('name', 'LIKE', '%'.$term.'%')->lists('product_id')->toArray();
          $subproducts = \Solunes\Product\App\Product::where('active',1)->where(function($query) use ($term, $product_bridge_items, $brands_ids) {
            $query->whereIn('id', $product_bridge_items);
            $query->orWhereIn('brand_id', $brands_ids);
          })->get();
          // $this->vardump( $subproducts );
          foreach($subproducts as $product){
              if($product_slug===true){
                  $products[] = ['name'=>$product->name, 'image'=>\asset(\Asset::get_image_path('product-image','detail',$product->image)), 'id'=>$product->product_bridge->slug];
              } else {
                  $products[] = ['name'=>$product->name, 'image'=>\asset(\Asset::get_image_path('product-image','detail',$product->image)), 'id'=>$product->product_bridge->id];
              }
          }
      }
      return $products;
  }


  public function ajaxOneSamsung(Request $request) {
    $isempty = false;
    $variations = $request->input('variation');
    // Adicionado por Josué
    $products = \Solunes\Product\App\Product::query();
    \Log::info(json_encode($variations));
    // if(count($variations)>0){
    //   $html = 'aqui 0';
    //   $products = \Solunes\Business\App\ProductBridge::whereHas('product_bridge_variation_option', function($q) use($variations){
    //      $q->whereIn('variation_option_id', $variations);
    //   });
    // } else {
    //   $html = 'aqui 1';
    //   $isempty = true;
    //   //var_dump($request->input('category'));
    //   if($request->input('category')){
    //     $products = \Solunes\Business\App\ProductBridge::where('category_id', $request->input('category'));
    //   } else{
    //     $products = \Solunes\Business\App\ProductBridge::where('id','>',0)->limit(24);
    //   }
    // }
    if(count($variations)>0){
      $html = 'aqui 0';
      $products = $products->whereHas('product_bridge_variation_option', function($q) use($variations){
          $q->whereIn('variation_option_id', $variations);
      });
    } else{
      $isempty = true;
      $products = $products->where('quantity', '>', 0)->where('id','>',0)->limit(24);
    }
    
    $html = 'aqui 1';
    if($request->input('category')){
      $products = $products->where('quantity', '>', 0)->where('category_id', $request->input('category'));
    } else{
      $products = $products->where('quantity', '>', 0)->where('id','>',0);
    }


    if($request->has('price_min')&&$request->input('price_min')){
      $html = 'aqui 2';
      $products = $products->where('price','>=',$request->input('price_min'));
    }
    if($request->has('price_max')&&$request->input('price_max')){
      $html = 'aqui 3';
      $products = $products->where('price','<=',$request->input('price_max'));
    }
    if($request->has('order')){
      if($request->input('order')=='ab'){
        $products = $products->get()->sortBy('name');
      } else if($request->input('order')=='minmax'){
        $products = $products->orderBy('price','ASC')->get();
      } else if($request->input('order')=='date'){
        $products = $products->orderBy('created_at','DESC')->get();
      } else {
        $products = $products->orderBy('created_at', 'DESC')->get();
      }
    } else {
      $products = $products->where('active',1)->orderBy('created_at', 'DESC')->get();
    }
    $html = '';
    foreach($products as $item){
      $html .= view('includes.product-summary', ['product'=>$item])->render();
      //$html .= '<h1>asdasdas</h1>';
    }
    $variations_html = NULL;
    return ['fields'=>count($request->all()),'variations_html'=>$variations_html,'html'=>$html, 'isempty'=>$isempty, 'count'=>$products->count()];
  }

  /* Ruta POST para editar el carro de compras */
  public function postUpdateCartTwo(Request $request) {
    if($cart = \Solunes\Sales\App\Cart::checkOwner()->checkCart()->status('holding')->orderBy('updated_at','DESC')->first()){
      $coupon_code = NULL;
      if($request->has('coupon_code')){
        $range_price = \Solunes\Business\App\PricingRule::where('active','1')->where('coupon_code', $request->input('coupon_code'))->first();
        if($range_price){
          $coupon_code = $request->input('coupon_code');
        }
      }
      foreach($cart->cart_items as $item){
        if(isset($request->input('product_id')[$item->id])&&$request->input('quantity')[$item->id]>0){
          $item->quantity = $request->input('quantity')[$item->id];
          //$item->discount_price = \Business::getProductDiscount($item->product_bridge, $item->quantity, $coupon_code);
          $item->save();
        } else {
          $item->delete();
        }
      }
      $cart->coupon_code = $coupon_code;
      $cart->save();
      return redirect($this->prev)->with('message_success', 'Se actualizó su carro de compras correctamente.');
    } else {
      return redirect($this->prev)->with('message_error', 'Hubo un error al actualizar su carro de compras.');
    }
  } 

  public function getFinishSale( $cart_id = NULL, $quotation = false ) {
    if(($cart_id&&$cart = \Solunes\Sales\App\Cart::findId($cart_id)->checkOwner()->status('holding')->orderBy('updated_at','DESC')->first())||($cart = \Solunes\Sales\App\Cart::checkOwner()->checkCart()->status('holding')->orderBy('updated_at','DESC')->first())){
      $array['country_id'] = config('sales.default_country');
      $array['city_id'] = config('sales.default_city');
      $array['city_other'] = NULL;
      if(config('business.agency_shippings')&&$cart->agency_id){
        $array['agency'] = $cart->agency;
      } else {
        $array['agency'] = \Solunes\Business\App\Agency::first();
      }

      if(config('solunes.inventory')){
        if(config('sales.check_cart_stock')&&$array['agency']->stockable){
          foreach($cart->cart_items as $cart_item){
            $product = $cart_item->product_bridge;
            $stock = \Business::getProductBridgeStock($product, $array['agency']->id);
            if($stock<1){
              $cart_item->delete();
            } else if($stock<$cart_item->quantity){
              $cart_item->quantity = $stock;
              $cart_item->save();
            }
          }
        }
      }

      $cart->load('cart_items');
      if(count($cart->cart_items)==0){
        return redirect($this->prev)->with('message_error', 'Lo sentimos, su compra no tiene ningún producto válido.');
      }      

      if(\Auth::check()){
        $user = \Auth::user();
        $array['auth'] = true;
        if('solunes.customer'&&$user->customer){
          if(config('solunes.addresses')){
            if($user->customer->main_customer_address->country_id){
              $array['country_id'] = $user->customer->main_customer_address->country_id;
            }
            if($user->customer->main_customer_address->city_id){
              $array['city_id'] = $user->customer->main_customer_address->city_id;
            }
            $array['city_other'] = $user->customer->main_customer_address->city_other;
            $array['address'] = $user->customer->main_customer_address->address;
            $array['address_extra'] = $user->customer->main_customer_address->address_extra;
          } else {
            if($user->customer->country_id){
              $array['country_id'] = $user->customer->country_id;
            }
            if($user->customer->city_id){
              $array['city_id'] = $user->customer->city_id;
            }
            $array['city_other'] = $user->customer->city_other;
            $array['address'] = $user->customer->address;
            $array['address_extra'] = $user->customer->address_extra;
          }
          $array['nit_number'] = $user->customer->nit_number;
          $array['nit_social'] = $user->customer->nit_name;
          if(config('sales.ask_coordinates')&&!$quotation&&$user->customer->latitude&&$user->customer->longitude){
            $array['map_coordinates'] = ['type'=>'customer', 'latitude'=>$user->customer->latitude, 'longitude'=>$user->customer->longitude];
          } else {
            $array['map_coordinates'] = ['type'=>'none', 'latitude'=>NULL, 'longitude'=>NULL];
          }
        } else {
          if($user->country_id){
            $array['country_id'] = $user->country_id;
          }
          if($user->city_id){
            $array['city_id'] = $user->city_id;
          }
          $array['city_other'] = $user->city_other;
          $array['address'] = $user->address;
          $array['address_extra'] = $user->address_extra;
          $array['nit_number'] = $user->nit_number;
          $array['nit_social'] = $user->nit_name;
          if(config('sales.ask_coordinates')&&!$quotation&&$user->latitude&&$user->longitude){
            $array['map_coordinates'] = ['type'=>'user', 'latitude'=>$user->latitude, 'longitude'=>$user->longitude];
          } else {
            $array['map_coordinates'] = ['type'=>'none', 'latitude'=>NULL, 'longitude'=>NULL];
          }
        }
      } else {
        session()->set('url.intended', url()->current());
        $array['auth'] = false;
        $array['address'] = NULL;
        $array['address_extra'] = NULL;
        $array['nit_number'] = NULL;
        $array['nit_social'] = NULL;
        if(config('sales.ask_coordinates')&&!$quotation){
          $array['map_coordinates'] = ['type'=>'none', 'latitude'=>NULL, 'longitude'=>NULL];
        }
      }
      if(config('sales.delivery_country')){
        $array['countries'] = \Solunes\Business\App\Country::get()->lists('name','id')->toArray();
        $region_ids = \Solunes\Business\App\Region::where('country_id', $array['country_id'])->lists('id')->toArray();
        $array['cities'] = \Solunes\Business\App\City::whereIn('region_id', $region_ids)->get()->lists('name','id')->toArray();
      } else {
        $array['cities'] = \Solunes\Business\App\City::get()->lists('name','id')->toArray();
      }
      $array['cart'] = $cart;
      $array['shipping_dates'] = [];
      $array['shipping_times'] = [];
      if(config('sales.delivery')){
        if(config('business.agency_shippings')&&$cart->agency_id){
          $array['shipping_options'] = \Solunes\Sales\App\Shipping::whereHas('agency_shipping', function($q) use($cart) {
            $q->where('agency_id', $cart->agency_id);
          })->active()->order()->lists('name','id');
          $array['shipping_descriptions'] = \Solunes\Sales\App\Shipping::whereHas('agency_shipping', function($q) use($cart) {
            $q->where('agency_id', $cart->agency_id);
          })->active()->order()->get();
          $first_shipping = \Solunes\Sales\App\Shipping::whereHas('agency_shipping', function($q) use($cart) {
            $q->where('agency_id', $cart->agency_id);
          })->active()->order()->first();
        } else {
          $array['shipping_options'] = \Solunes\Sales\App\Shipping::active()->order()->lists('name','id');
          $array['shipping_descriptions'] = \Solunes\Sales\App\Shipping::active()->order()->get();
          $first_shipping = \Solunes\Sales\App\Shipping::active()->order()->first();
        }
        if(config('sales.delivery_select_day')&&$first_shipping){
          $first_shipping_city = $first_shipping->shipping_city()->where('city_id', $array['city_id'])->first();
          if(!$first_shipping_city){
            $first_shipping_city = $first_shipping->shipping_city;
          }
          $array['shipping_dates'] = \Sales::getShippingDates($first_shipping, $first_shipping_city->shipping_days);
        }
        if(config('sales.delivery_select_hour')&&$first_shipping){
          $array['shipping_times'] = $first_shipping->shipping_times()->lists('name','id')->toArray();
        }
      } else {
        $array['shipping_options'] = [];
        $array['shipping_descriptions'] = [];
      }
      if(config('business.agency_payment_methods')&&$cart->agency_id){
        $array['payment_options'] = \Solunes\Payments\App\PaymentMethod::whereHas('agency_payment_method', function($q) use($cart) {
          $q->where('agency_id', $cart->agency_id);
        })->active()->order()->lists('name','id');
        $array['payment_descriptions'] = \Solunes\Payments\App\PaymentMethod::whereHas('agency_payment_method', function($q) use($cart) {
          $q->where('agency_id', $cart->agency_id);
        })->active()->order()->get();
      } else {
        $array['payment_options'] = \Solunes\Payments\App\PaymentMethod::active()->order()->lists('name','id');
        $array['payment_descriptions'] = \Solunes\Payments\App\PaymentMethod::active()->order()->get();
      }
      $array['page'] = \Solunes\Master\App\Page::find(2);
      $total = 0;
      $weight = 0;
      foreach($cart->cart_items as $cart_item){
        $total += $cart_item->total_price;
        $weight += $cart_item->total_weight;
      }
      $array['total'] = $total;
      $array['weight'] = $weight;
      if($quotation){
        $array['quotation'] = $quotation;
      } else {
        $array['quotation'] = false;
      }
      if(config('sales.ask_coordinates')&&!$quotation&&$array['map_coordinates']['type']=='none'){
        $coordinates = config('solunes.default_location');
        $coordinates = explode(';',$coordinates);
        $array['map_coordinates'] = ['type'=>'default', 'latitude'=>$coordinates[0], 'longitude'=>$coordinates[1]];
      }
      $view = 'process.finalizar-compra';
      if(!view()->exists($view)){
        $view = 'sales::'.$view;
      }
      return view($view, $array);
    } else {
      return redirect('')->with('message_error', 'No se encuentra el producto para ser comprado.');
    }
  }

  public function postFinishSale( Request $request ) {
    \Artisan::call('fix-sales-status');
    $cart_id = $request->input('cart_id');
    if(auth()->check()){
      $rules = \Solunes\Sales\App\Sale::$rules_auth_send;
    } else {
      $rules = \Solunes\Sales\App\Sale::$rules_send;
    }
    if(!config('sales.delivery')){
      unset($rules['shipping_id']);
    }
    if(!config('sales.delivery_city')){
      unset($rules['city_id']);
    }
    if(!config('sales.ask_address')||$request->has('quotation')){
      unset($rules['address']);
    }
    if(!config('sales.sales_email')){
      unset($rules['email']);
    }
    if(!config('sales.sales_cellphone')){
      unset($rules['cellphone']);
    }
    if(!config('sales.sales_username')){
      unset($rules['username']);
    }
    if(!config('sales.ask_invoice')||$request->has('quotation')){
      unset($rules['nit_number']);
      unset($rules['nit_social']);
    }
    $validator = \Validator::make($request->all(), $rules);
    if(!$validator->passes()){
      return redirect($this->prev)->with('message_error', 'Debe llenar todos los campos obligatorios.')->withErrors($validator)->withInput();
    } else if($cart_id&&$cart = \Solunes\Sales\App\Cart::findId($cart_id)->checkOwner()->status('holding')->first()){
      $new_user = false;
      if($request->has('quotation')&&$request->input('quotation')!='false'&&$request->input('quotation')!=false){
        $quotation = $request->input('quotation');
      } else {
        $quotation = false;
      }

      if(config('sales.sales_agency')){
        if($cart->agency_id){
          $agency = $cart->agency;
        } else {
          $agency = \Solunes\Business\App\Agency::find(config('business.online_store_agency_id')); // Parametrizar tienda en config
        }
      } else {
        $agency = \Solunes\Business\App\Agency::find(config('business.online_store_agency_id')); // Parametrizar tienda en config
      }
      $order_cost = 0;
      $order_weight = 0;
      $discount_amount = 0;
      foreach($cart->cart_items as $item){
        $order_cost += $item->total_price;
        $order_weight += $item->total_weight;
        if(config('payments.sfv_version')>1||config('payments.discounts')){
          $discount_amount += $item->discount_price;
        }
        if(config('solunes.inventory')&&$agency->stockable){
          $stock = \Business::getProductBridgeStockItem($item->product_bridge, $agency->id);
          if($stock){
            if(is_integer($stock)){
              if($stock<$item->quantity){
                //$stock->quantity = $stock->quantity - $item->quantity;
                return redirect($this->prev)->with('message_error', 'El item "'.$item->product_bridge->name.'" no cuenta con stock suficiente. Actualmente tiene "'.$stock.'" unidades disponibles.')->withInput();
              } else if(!$stock) {
                //$stock->quantity = 0;
                return redirect($this->prev)->with('message_error', 'El item "'.$item->product_bridge->name.'" no cuenta con stock.')->withInput();
              }
            } else {
              if($stock->quantity<$item->quantity){
                //$stock->quantity = $stock->quantity - $item->quantity;
                return redirect($this->prev)->with('message_error', 'El item "'.$item->product_bridge->name.'" no cuenta con stock suficiente. Actualmente tiene "'.$stock->quantity.'" unidades disponibles.')->withInput();
              } else if(!$stock) {
                //$stock->quantity = 0;
                return redirect($this->prev)->with('message_error', 'El item "'.$item->product_bridge->name.'" no cuenta con stock.')->withInput();
              }
            }
            //$stock->save();
          }
        }
      }
      if(config('sales.delivery')){
        $shipping_array = \Sales::calculate_shipping_cost($request->input('shipping_id'), $request->input('country_id'), $request->input('city_id'), $order_weight, $request->input('map_coordinates'),null);
        if($shipping_array['shipping']===false){
          return redirect($this->prev)->with('message_error', 'No se encontró el método de envío para esta ciudad, seleccione otro.')->withInput();
        }
        $shipping_cost = $shipping_array['shipping_cost'];
      } else {
        $shipping_cost = 0;
      }

      // User
      if(config('solunes.customer')){
        $customer = \Sales::customerRegistration($request);
        $user = $customer->user;
      } else {
        $customer = NULL;
        $user = \Sales::userRegistration($request);
      }
      if(is_string($user)){
        return redirect($this->prev)->with('message_error', 'Hubo un error al finalizar su registro: '.$user);
      }
      
      // Sale
      if(config('business.pricing_rules')){
        $past_order_cost = $order_cost;
        $order_cost = \Business::getSaleDiscount($order_cost, $cart->coupon_code);
      }
      $total_cost = $order_cost + $shipping_cost;
      $currency = $item->currency;
      $sale = new \Solunes\Sales\App\Sale;
      $sale->user_id = $user->id;
      if($customer){
        $sale->customer_id = $customer->id;
      }
      $sale->coupon_code = $cart->coupon_code;
      if($quotation){
        $sale->lead_status = 'quotation-request';
      } else {
        $sale->lead_status = 'sale';
      }
      if(config('sales.sales_agency')){
        $sale->agency_id = $agency->id;
      }
      $sale->currency_id = $currency->id;
      $sale->order_amount = $order_cost;
      $sale->amount = $total_cost;
      if(config('sales.ask_invoice')&&!$quotation){
        if(config('sales.generate_invoice_pagostt')){
          $sale->invoice = true;
        } else {
          $sale->invoice = false;
        }
        $sale->invoice_nit = $request->input('nit_number');
        $sale->invoice_name = $request->input('nit_social');
      } else {
        $sale->invoice = false;
      }
      //$sale->type = 'online';
      $sale->save();
      if($quotation){
        $sale->name = 'Cotización Online: #'.$sale->id;
      } else {
        $sale_name = 'Venta Online: #'.$sale->id;
        $sale->load('sale_items');
        $count_sale_items = count($cart->cart_items);
        if($count_sale_items>1){
          $sale_name .= ' (x'.$count_sale_items.' items)';
        } else if($count_sale_items==1){
          $sale_name .= ' (x1 item)';
        }
        $sale->name = $sale_name;
      }
      $sale->save();

      // Sale Payment
      if(!$quotation){
        $sale_payment = new \Solunes\Sales\App\SalePayment;
        $sale_payment->parent_id = $sale->id;
        $sale_payment->payment_method_id = $request->input('payment_method_id');
        $sale_payment->currency_id = $currency->id;
        $sale_payment->exchange = $currency->main_exchange;
        $sale_payment->amount = $past_order_cost;
        if(config('payments.sfv_version')>1||config('payments.discounts')){
          $sale_payment->discount_amount = $discount_amount;
        }
        if(config('sales.delivery')){
          $sale_payment->pay_delivery = 1;
        }
        $sale_payment->pending_amount = $past_order_cost;
        $sale_payment->detail = 'Pago por compra online: #'.$sale_payment->id;
        $sale_payment->save();
      }

      // Sale Delivery
      if(config('sales.delivery')){
        $shipping = \Solunes\Sales\App\Shipping::find($request->input('shipping_id'));
        if($shipping){
          $sale_delivery = new \Solunes\Sales\App\SaleDelivery;
          $sale_delivery->parent_id = $sale->id;
          $sale_delivery->shipping_id = $request->input('shipping_id');
          $sale_delivery->currency_id = $sale->currency_id;
          if(config('sales.delivery_city')){
            if(config('sales.delivery_country')){
              $sale_delivery->country_code = $user->city->region->country->name;
            } else {
              $sale_delivery->country_code = 'BO';
            }
            $sale_delivery->region_id = $user->city->region_id;
            $sale_delivery->city_id = $user->city->id;
            if($request->has('city_other')){
              $sale_delivery->city_other = $request->input('city_other');
            }
            if($request->has('region_other')){
              $sale_delivery->region_other = $request->input('region_other');
            }
          } else {
            $sale_delivery->region_id = 1;
            $sale_delivery->city_id = 1;
          }
          if(config('sales.delivery_select_day')&&$request->has('shipping_date')){
            $sale_delivery->shipping_date = $request->input('shipping_date');
          }
          if(config('sales.delivery_select_hour')&&$request->has('shipping_time_id')){
            $sale_delivery->shipping_time_id = $request->input('shipping_time_id');
          }
          $sale_delivery->name = 'Pedido de venta en linea';
          $sale_delivery->address = $request->input('address');
          $sale_delivery->address_extra = $request->input('address_extra');
          $sale_delivery->postal_code = 'LP01';
          $sale_delivery->phone = $request->input('cellphone');
          $sale_delivery->total_weight = $order_weight;
          $sale_delivery->shipping_cost = $shipping_cost;
          if($shipping_city = $shipping->shipping_cities()->where('city_id', $sale_delivery->city_id)->first()){
            $delivery_time = $shipping_city->shipping_days;
          } else {
            $delivery_time = 1;
          }
          if($delivery_time==1){
            $sale_delivery->delivery_time = $delivery_time.' día';
          } else {
            $sale_delivery->delivery_time = $delivery_time.' días';
          }
          if(config('sales.ask_coordinates')&&!$quotation){
            $coordinates = $request->input('map_coordinates');
            if($coordinates){
              $coordinates = explode(';', $coordinates);
              if(isset($coordinates[0])&&isset($coordinates[1])){
                $sale_delivery->latitude = $coordinates[0];
                $sale_delivery->longitude = $coordinates[1];
                if(!$customer->latitude&&!$customer->longitude){
                  $customer->latitude = $coordinates[0];
                  $customer->longitude = $coordinates[1];
                  $customer->save();
                }
              }
            }
          }
          $sale_delivery->save();
        }
      }

      // Sale Items
      $store_agency = $agency;
      foreach($cart->cart_items as $cart_item){
        $product_bridge = $cart_item->product_bridge;
        $sale_item = new \Solunes\Sales\App\SaleItem;
        $sale_item->parent_id = $sale->id;
        $sale_item->product_bridge_id = $cart_item->product_bridge_id;
        //$sale_item->name = $cart_item->product_bridge->name;
        $sale_item->currency_id = $currency->id;
        $sale_item->detail = $cart_item->detail;
        $sale_item->price = $cart_item->price;
        $sale_item->quantity = $cart_item->quantity;
        if(config('payments.sfv_version')>1){
          $sale_item->economic_sin_activity = $product_bridge->economic_sin_activity;
          $sale_item->product_sin_code = $product_bridge->product_sin_code;
          $sale_item->product_internal_code = $product_bridge->product_internal_code;
          $sale_item->product_serial_number = $product_bridge->product_serial_number;
        }
        if(config('payments.sfv_version')>1||config('payments.discounts')){
          $sale_item->discount_price = $cart_item->discount_price;
          $sale_item->discount_amount = round($cart_item->discount_price * $cart_item->quantity);
        }
        //$sale_item->weight = $cart_item->weight;
        $sale_item->save();
        if(config('solunes.inventory')&&!config('inventory.reduce_inventory_after_purchase')&&$sale_item->product_bridge->stockable&&!$quotation){
          \Inventory::reduce_inventory($store_agency, $sale_item->product_bridge, $sale_item->quantity);
        }
      }

      $cart->status = 'sale';
      $cart->user_id = $user->id;
      $cart->save();

      $sale->updated_at = NULL;
      if(config('sales.sale_duration_hours')){
        $now = new \DateTime('+'.config('sales.sale_duration_hours').' hours');
        $sale->expiration_date = $now->format('Y-m-d');
        $sale->expiration_time = $now->format('H:i:s');
      }
      $sale->save();

      // datos para la compra
      if( $request->has('gift') ) {
        $sale->gift           = 1;
        $sale->gift_name      = $request->input('gift_name');
        $sale->gift_cellphone = $request->input('gift_cellphone');
        $sale->gift_message   = $request->input('gift_message');
        $sale->save();
      }

      // Send Email
      if($quotation){
        //$vars = ['@name@'=>$user->name, '@total_cost@'=>$sale->total_cost, '@sale_link@'=>url('process/sale/'.$sale->id)];
        //\FuncNode::make_email('new-sale', [$user->email], $vars);
      } else {
        \Payments::generatePayment($sale); // Generar pagos de la venta
        $vars = ['@name@'=>$user->name, '@total_cost@'=>$sale->total_cost, '@sale_link@'=>url('process/sale/'.$sale->id)];
        \FuncNode::make_email('new-sale', [$user->email], $vars);
      }

      $redirect = 'process/sale/'.$sale->id;
      if($quotation){
        return redirect($redirect)->with('message_success', 'Su cotización fue generada correctamente.');
      }
      // Revisar redirección a método de pago antes a PAGOSTT, TODO: Configurar para Paypal y Otros
      if(config('sales.redirect_to_payment')&&$sale_payment->payment_method->code=='pagostt'){
        $model = '\\'.$sale_payment->payment_method->model;
        return \Payments::generateSalePayment($sale, $model, $redirect, NULL);
      }

      // return redirect($redirect)->with('message_success', 'Su compra fue confirmada correctamente, ahora debe proceder al pago para finalizarla.');
      return redirect($redirect)->with('message_success', 'Registro realizado correctamente, ahora debe proceder al pago para proceder su compra.');
    } else {
      return redirect($this->prev)->with('message_error', 'Hubo un error al actualizar su carro de compras.');
    }    
  }
	protected function vardump() {    
		$arg_list = func_get_args();
		foreach ( $arg_list as $variable ) {
			echo '<pre style="color: #000; background-color: #fff;">';
			echo htmlspecialchars( var_export( $variable, true ) );
			echo '</pre>';
		}
	}

  public function ajaxFillModelsByBrand( Request $request ) {
    $vehicle_models = \App\VehicleModel::where('vehicle_brand_id', $request->vehicle_brand_id)->get();
    return [
      'status' => true,
      'models' => $vehicle_models
    ];
  }

}
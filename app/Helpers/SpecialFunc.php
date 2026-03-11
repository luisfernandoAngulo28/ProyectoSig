<?php 

namespace App\Helpers;
use Facebook\Facebook;
class SpecialFunc {

        /* OBTENER URL DE LA IMAGEN */
    public static function send_email_recover_password( $correo ) {
        // postRecoverPasswordRabbit
        // return \Asset::get_image_path( $folder, $code, $file );

        return  \Customer::recoverPassword($correo, null);
    }

        /* OBTENER URL DE LA IMAGEN */
    public static function get_image_path( $folder, $code, $file ) {
        return \Asset::get_image_path( $folder, $code, $file );
    }

        /* CARGAR ARCHIVO (URL) IMAGEN */
    public static function upload_image( $file, $folder, $image_name = null ) {
        return \Asset::upload_image_2( $file, $folder, $image_name );
    }

        /* OBTENER URL DE ARCHIVO */
    public static function get_file( $folder, $file ) {
        return \Asset::get_file( $folder, $file );
    }

        /* CARGAR URL DE ARCHIVO */
    public static function upload_file( $file, $folder ) {
        return \Asset::upload_file( $folder, $file );
    }

        /* ENVIAR EMAIL A TRAVES DE UN BOCETO EN LA TABLA EMAILS */
    public static function make_email( $email_name, $array_emails = [], $vars = [] ) {
        return \FuncNode::make_email( $email_name, $array_emails, $vars );
    }

        /* Crear notificaciones a nivel subadmin */
    public static function make_dashboard_notitification( $name, $user_ids, $url, $message ) {
        return \FuncNode::make_dashboard_notitification( $name, $user_ids, $url, $message );
    }

    public static function send_email( $email_title, $array_emails, $message_title, $message_content ) {
        return \Notification::sendEmail( $email_title, $array_emails, $message_title, $message_content );
    }

        /* SMS AWS */
    public static function send_sms( $number, $message, $sender = null, $transactional = false, $country_code = '+591' ) {
        return \Notification::sendSms( $number, $message, $sender, $transactional, $country_code );
    }

        /* SMS Twilio */
    public static function send_sms_twilo( $number, $message, $sender = null, $country_code = '+591' ) {
        return \Notification::sendSmsTwilo( $number, $message, $sender, $country_code );
    }

    public static function create_product_bridge( $item, $type, $name ) {
        if(!$product_bridge = \Solunes\Business\App\ProductBridge::where( 'product_type', $type )->where('product_id', $item->id)->first()){
            $product_bridge = new \Solunes\Business\App\ProductBridge;
            $product_bridge->product_type = $type;
            $product_bridge->product_id   = $item->id;
        }
        $product_bridge->currency_id = 1;
        $product_bridge->price       = $item->price;
        $product_bridge->name        = $name;
        $product_bridge->save();

        if($item->product_bridge_id!=$product_bridge->id){
            $item->product_bridge_id = $product_bridge->id;
            $item->save();
        }
        return $product_bridge;
    }

    public static function make_ride_sale( 
        $user_id,
        $customer_id,
        $ride_id,
        $detail,
        $payment_method_id,
        $invoice_name,
        $invoice_number,
        $invoice = 1,
        $currency_id = 1,
        $quantity = 1,
        $app_key = null
    ) {
        $ride = \App\Ride::find($ride_id);
        if( !$ride ) {
            return ['status'=>false, 'message'=>'ID de viaje no encontrado, verifique nuevamene'];
        }
        $product_bridge = \SpecialFunc::create_product_bridge( $ride, 'ride', $detail );
        if( $payment_method_id == 3 ) {
            config(['payments.pagostt_params' => config('payments.todotix_params')]);
            config(['payments.app_key'        => $app_key ?? config('payments.todotix_params.app_key') ]);
            config(['payments.test_app_key'   => $app_key ?? config('payments.todotix_params.test_app_key') ]);
        }

        $ride->product_bridge_id =  $product_bridge->id;
        $ride->save();

        $sale = \Sales::generateSingleSale( 
            $user_id, 
            $customer_id, 
            $currency_id, 
            $payment_method_id,
            $invoice, 
            $invoice_name, 
            $invoice_number, 
            $detail, 
            $ride->total_price, 
            $product_bridge->id, 
            $quantity
        );
        $ride->sale_id = $sale->id;
        $ride->save();


        $payment_method = \Solunes\Payments\App\PaymentMethod::find($payment_method_id);


        return \SpecialFunc::get_finish_sale_payment( $sale->id, $payment_method->code );
   
    }

    public static function generate_sale_payment( $sale, $model_name, $redirect, $type ) {

        $payment = \Payments::generatePayment($sale);

        $cancel_url = url('payments/finish-payment/'.$payment->id);
        $model = new $model_name;

        if($model_name=='\OmnipayGateway'){
            $api_url = $model->generateSalePayment($payment, $cancel_url, $type);
        } else {
          $api_url = $model->generateSalePayment($payment, $cancel_url);
        }

        if($api_url){
            return [ 
                'status' => true, 
                'message'=>'URL generada con éxito.',
                'data'   => [
                    'url' => $api_url
                ], 
            ];
        } else {
            return [ 'status'=>false, 'message'=>['Hubo un error al realizar el pago de la compra pendiente.'] ];
        }
    }

    public static function get_finish_sale_payment( $sale_id, $type ) {
        $sale = \Solunes\Sales\App\Sale::find($sale_id);
        $model = '\Pagostt';
        if($type=='pagostt'){
          $model = '\Pagostt';
        } else if($type=='pagatodo'){
          $model = '\Pagatodo';
        } else if($type=='banipay'){
          $model = '\Banipay';
        } else if($type=='paypal'||$type=='braintree'||$type=='payu'||$type=='neteller'){
          $model = '\OmnipayGateway';
        } else if($type=='payme'){
          $model = '\Payme';
        } else if($type=='test-payment'&&config('payments.test-payment')){
          $model = '\TestPayment';
        } else if($type=='bank-deposit'){
          $model = '\BankDeposit';
        }
        return \SpecialFunc::generate_sale_payment( $sale, $model, 'inicio', $type );
    }
}
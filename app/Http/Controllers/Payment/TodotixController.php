<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TodotixController extends Controller {

	protected $request;
	protected $url;

	public function __construct() {
	}

  public function getMakePayment($payment_id) {
    if($payment_id&&$customer_payment = \App\CustomerPayment::find($payment_id)){

      $customer = $customer_payment->customer;
      $user = auth()->user();

      $final_fields = array(
        "appkey" => 'c26d8c99-8836-4cd5-a850-230c9d3fbf3c',
        "email_cliente" => $user->email,
        "descripcion" => "Pago de Mensualidades",
        "callback_url" => url('admin/my-payments').'/?success=done',
        "razon_social" => $customer->nit_name,
        "nit" => $customer->nit_number,
        "valor_envio" => 0,
        "descripcion_envio" => 'Sin costo de envío'
      );

      // Generación de items para el carro al detalle de Todotix
      $fields = [];
      foreach($customer_payment->payment_items as $payment_item){
        $sub_field = [];
        $sub_field['concepto'] = $payment_item->name;
        $sub_field['cantidad'] = $payment_item->quantity;
        $sub_field['costo_unitario'] = $payment_item->amount;
        $fields[] = json_encode($sub_field);
      }

      $final_fields['lineas_detalle_deuda'] = $fields;

      // Consulta CURL a Web Service
      $urlhere = 'http://www.todotix.com:10365/rest/deuda/registrar';
      $ch = curl_init();
      $options = array(
          CURLOPT_URL            => $urlhere,
          CURLOPT_POST           => true,
          CURLOPT_POSTFIELDS     => json_encode($final_fields),
          CURLOPT_RETURNTRANSFER => true,
      );
      curl_setopt_array($ch, $options);
      $result = curl_exec($ch);
      curl_close($ch);  

      $product_result = json_decode($result);

      $transaction_id = $product_result->id_transaccion;
      $api_url = $product_result->url_pasarela_pagos;

      // Generación de Transacción y Redirección
      /*if(count($sale->payment_receipts)>0){
        
      } else {
        $sale_payment = \Store::create_sale_payment($payment, $sale, $sale->amount, 'Detalle');
      }*/

      return redirect($api_url);
    } else {
      return redirect($this->prev)->with('message_error', 'Hubo un error al encontrar su compra.');
    }
  }

  public function getMakePaymentForAll() {
    $user = auth()->user();
    if($user&&$customer = $user->customers()->first()){   
      
      $final_fields = array(
        "appkey" => 'c26d8c99-8836-4cd5-a850-230c9d3fbf3c',
        "email_cliente" => $user->email,
        "descripcion" => "Pago de Mensualidades",
        "callback_url" => url('admin/my-payments').'/?success=done',
        "razon_social" => $customer->nit_name,
        "nit" => $customer->nit_number,
        "valor_envio" => 0,
        "descripcion_envio" => 'Sin costo de envío'
      );

      // Generación de items para el carro al detalle de Todotix
      $fields = [];
      foreach($customer->customer_payments()->where('status','pending')->get() as $customer_payment){
        foreach($customer_payment->payment_items as $payment_item){
          $sub_field = [];
          $sub_field['concepto'] = $payment_item->name;
          $sub_field['cantidad'] = $payment_item->quantity;
          $sub_field['costo_unitario'] = $payment_item->amount;
          $fields[] = json_encode($sub_field);
        }
      }

      $final_fields['lineas_detalle_deuda'] = $fields;

      // Consulta CURL a Web Service
      $urlhere = 'http://www.todotix.com:10365/rest/deuda/registrar';
      $ch = curl_init();
      $options = array(
          CURLOPT_URL            => $urlhere,
          CURLOPT_POST           => true,
          CURLOPT_POSTFIELDS     => json_encode($final_fields),
          CURLOPT_RETURNTRANSFER => true,
      );
      curl_setopt_array($ch, $options);
      $result = curl_exec($ch);
      curl_close($ch);  

      $product_result = json_decode($result);

      $transaction_id = $product_result->id_transaccion;
      $api_url = $product_result->url_pasarela_pagos;

      // Generación de Transacción y Redirección
      /*if(count($sale->payment_receipts)>0){
        
      } else {
        $sale_payment = \Store::create_sale_payment($payment, $sale, $sale->amount, 'Detalle');
      }*/

      return redirect($api_url);
    } else {
      return redirect($this->prev)->with('message_error', 'Hubo un error al encontrar su compra.');
    }
  }

}
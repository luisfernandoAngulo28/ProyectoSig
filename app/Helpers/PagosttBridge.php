<?php 

namespace App\Helpers;

class PagosttBridge {

    // Encontrar cliente en sistema o devolver nulo
    public static function getCustomer($customer_id, $get_pending_payments = false, $for_api = false) {
        if($customer = \App\User::where('id',$customer_id)->first()){
            // Definir variables de cliente en formato PagosTT: email, name, nit_name, nit_number
            $array = [];
            $array['id'] = $customer->id;
            if(config('services.enable_test')){
                $array['email'] = 'edumejia30@gmail.com';
            } else {
                $array['email'] = $customer->email;
            }
            $array['ci_number'] = $customer->username;
            $array['name'] = $customer->name;
            $array['first_name'] = $customer->first_name;
            $array['last_name'] = $customer->last_name;
            if(config('sales.ask_invoice')){
                $array['nit_name'] = $customer->nit_name;
                $array['nit_number'] = $customer->nit_number;
            } else {
                $array['nit_name'] = $customer->name;
                $array['nit_number'] = $customer->username;
            }
            // Consultar y obtener los pagos pendientes del cliente en formato PagosTT: concepto, cantidad, costo_unitario
            $pending_payments = [];
            if($get_pending_payments&&config('payments.pagostt_params.customer_all_payments')){
                foreach($customer->pending_payments as $payment){
                    if($for_api){
                        $pending_payments[$payment->id]['name'] = $payment->name;
                        $pending_payments[$payment->id]['due_date'] = $payment->due_date;
                    }
                    if(config('services.enable_test')==1){
                        $pending_payments[$payment->id]['amount'] = count($payment->payment_items);
                    } else {
                        $pending_payments[$payment->id]['amount'] = $payment->amount;
                    }
                    foreach($payment->payment_items as $payment_item){
                        if(config('services.enable_test')==1){
                            $amount = 1;
                        } else {
                            $amount = $payment_item->amount;
                        }
                        $pending_payment = \Pagostt::generatePaymentItem($payment_item->name, $payment_item->quantity, $amount, $payment->has_invoice);
                        $pending_payments[$payment->id]['items'][] = $pending_payment;
                    }
                }
                $array['payment']['name'] = 'Múltiples Pagos';
                $array['payment']['has_invoice'] = $payment->invoice;
                //$array['payment']['metadata'][] = \Pagostt::generatePaymentMetadata('Tipo de Cambio', $payment->exchange);
            }
            $array['pending_payments'] = $pending_payments;
            return $array;
        } else {
            return NULL;
        }
    }

    // Encontrar pago en sistema o devolver nulo
    public static function getPayment($payment_id) {
        if($payment = \Solunes\Payments\App\Payment::where('id', $payment_id)->where('status','holding')->first()){
            // Definir variables de pago en formato PagosTT: name, items[concepto, cantidad, costo_unitario]
            $item = [];
            $item['id'] = $payment->id;
            $item['name'] = $payment->name;
            $subitems_array = [];
            foreach($payment->payment_items as $payment_item){
                if(config('services.enable_test')==1){
                    $amount = 1;
                } else {
                    $amount = $payment_item->amount;
                }
                $subitems_array[] = \Pagostt::generatePaymentItem($payment_item->name, $payment_item->quantity, $amount, $payment->has_invoice);
            }
            if(config('customer.enable_test')==1){
                $item['amount'] = count($payment->payment_items);
            } else {
                $item['amount'] = $payment->amount;
            }
            $item['items'] = $subitems_array;
            $item['has_invoice'] = $payment->invoice;
            //$item['metadata'][] = \Pagostt::generatePaymentMetadata('Tipo de Cambio', $payment->exchange);
            return $item;
        } else {
            return NULL;
        }
    }

    // Procesar pagos dentro del sistema luego de que la transacción fue procesada correctamente
    public static function transactionSuccesful($transaction) {
        $date = date('Y-m-d');
        if($transaction&&$transaction->status=='paid'){
            foreach($transaction->transaction_payments as $transaction_payment){
                $transaction_payment->processed = 1;
                $transaction_payment->save();
                $payment = $transaction_payment->payment;
                if($transaction_invoice = $transaction->transaction_invoice){
                    $payment->invoice = 1;
                    $payment->invoice_name = $transaction_invoice->customer_name;
                    $payment->invoice_nit = $transaction_invoice->customer_nit;
                    $payment->invoice_url = $transaction_invoice->invoice_url;
                }
                $payment->status = 'paid';
                $payment->payment_date = $date;
                $payment->save();
            }
            return true;
        } else {
            return false;
        }
    }

}
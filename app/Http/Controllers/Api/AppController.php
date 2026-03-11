<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller\Api;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AppController extends BaseController {
    
    public function postLogin(Request $request){
        if($request->has('email')&&$request->has('password')){
            $item = \App\User::where('email', $request->input('email'))->orWhere('username', $request->input('email'))->first();

            // Autentificar
            $credentials = $request->only('email', 'password');
            try {
                // verify the credentials and create a token for the user
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'Su usuario y contraseña no coinciden.'], 401);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'Hubo un error, vuelva a intentarlo.'], 500);
            }
            $date = date('d/n/Y H:i:s', strtotime('+6 months'));
            $user = auth()->user();
            if($request->has('token')){
                $token_notification = $request->input('token');
                if($token_notification&&!\App\Device::where('token', $token_notification)->first()){
                    $device = new \App\Device;
                    $device->user_id = $user->id;
                    $device->token = $token_notification;
                    $device->save();
                }
            } else {
                \Log::info(json_encode($request->all()));
            }

            $school = $user->school;

            if(!$school){
                throw new \Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException('Su usuario no tiene una escuela asociada.');
            }

            return ['token'=>$token, 'expirationDate'=>$date, 'schoolId'=>$school->id, 'schoolName'=>$school->name.' '.$school->city];
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException('Hubo un error.');
        }
    }

    public function getCheckLogin(){
        if(auth()->check()){
            return ['auth'=>true];
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException('No se encontró su sesión.');
        }
    }

    public function getNfcTags(){
        if(auth()->check()){
            $items = [];
            $user = auth()->user();
            if($school = $user->school){
                foreach($school->nfcs as $key => $item){
                    if($item->school_registration){
                        $items[$key]['nfc_tag'] = $item->secret_uid;
                        $items[$key]['name'] = $item->school_registration->customer_dependant->name;
                        $items[$key]['status'] = $item->school_registration->status;
                    }
                }
            }
            return ['items'=>$items, 'count'=>count($items)];
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException('Hubo un error.');
        }
    }

    public function postRegisterAssistance(Request $request){
        if(auth()->check()&&$request->has('nfc_tags')&&is_array($request->input('nfc_tags'))){
            $user = auth()->user();
            $nfc_tags = $request->input('nfc_tags');
            $recieved_tags = [];
            $date = date('d/n/Y H:i:s');
            foreach($nfc_tags as $nfc_object){
                \Log::info('Probando: '.json_encode($nfc_object));
                if(is_array($nfc_object)&&isset($nfc_object['nfc'])&&$nfc = \App\Nfc::where('secret_uid', $nfc_object['nfc'])->first()){
                    $school_registration = $nfc->school_registration;
                    $school = $school_registration->school;
                    $customer_dependant = $nfc->customer_dependant;
                    $customer = $customer_dependant->parent;
                    $item = new \App\SchoolAssistance;
                    $item->school_id = $nfc->school_id;
                    $item->school_registration_id = $school_registration->id;
                    $item->customer_id = $customer->id;
                    $item->customer_dependant_id = $customer_dependant->id;
                    $item->timestamp = time();
                    $item->date = $nfc_object['date'];
                    $item->time_in = $nfc_object['time'];
                    $item->name = $item->date.' - '.$item->time_in;
                    $item->save();
                    $email_code = NULL;
                    $sms_message = NULL;
                    $vars = ['@parent_name@'=>$customer->name, '@name@'=>$customer_dependant->name, '@school@'=>$school->name, '@time@'=>$nfc_object['time']];
                    if($school_registration->status=='active'){
                        $email_code = 'parent_access';
                        $sms_message = '"'.$customer_dependant->name.'" accedió a Escuelas Bolivar "'.$school->name.'" ahora a las '.$nfc_object['time'];
                    } else if($school_registration->status=='warning'){
                        $email_code = 'parent_warning';
                        $sms_message = '"'.$customer_dependant->name.'" accedió a "'.$school->name.'" ahora a las '.$nfc_object['time'].' sin embargo debe regular sus deudas pendientes';
                    } else if($school_registration->status=='banned'){
                        $email_code = 'parent_banned';
                        $sms_message = '"'.$customer_dependant->name.'" intentó acceder a "'.$school->name.'" ahora  sin embargo su acceso fue rechazado por deudas acumuladas que debe subsanar';
                    }
                    if($customer->email&&$email_code){
                        \FuncNode::make_email($email_code, [$customer->email], $vars);
                    }
                    if($customer->phone&&$sms_message){
                        \Func::send_sms('+591'.$customer->phone, $sms_message, 'ClubBolivar', true);
                    }
                    $recieved_tags[] = $nfc['secret_uid'];
                }
            }
            return ['registered'=>true, 'items'=>$recieved_tags, 'count'=>count($recieved_tags), 'date'=>$date];
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException('Hubo un error.');
        }
    }

    public function postGeneratePayment(Request $request){
		try {
        
		$validator = \Validator::make($request->all(), [
            // 'user_id' => 'required',
            // 'customer_id' => 'required',
            'ride_id' => 'required',
        ],[
            // 'user_id.required' => 'El campo user_id es requerido.',
            // 'customer_id.required' => 'El campo customer_id es requerido.',
            'ride_id.required' => 'El campo ride_id es requerido.',
        ]);

		if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Debes enviar los parámetros',
                'errors' => $validator->errors()->all(),
            ], 404);
        }

		$ride = \App\Ride::where('id', $request->input('ride_id'))->first();

		if(!$ride) 
			return response()->json([
				'status' => false,
				'message' => 'ID de viaje no encontrado, verifique nuevamene',
				'errors' => $validator->errors()->all(),
			], 404);
            
        $userCobra = \App\Driver::where('id', $ride->driver_id)->first();
        $userPaga = \App\User::where('id',   $ride->parent->user_id)->first();

        $invoice_name;
        $invoice_number;
        
        if($userPaga->nit_name == null || $userPaga->nit_name == '' ){
            $invoice_name = 'Sin nombre';
        }else{
            $invoice_name =$userPaga->nit_name;   
        }
        if($userPaga->nit_number == null || $userPaga->nit_number == '' ){
            $invoice_number = 1;
        }else{
            $invoice_number =$userPaga->nit_number;   
        }

		
        $response = \SpecialFunc::make_ride_sale(
			$userCobra->user_id,
            $userPaga->id, // el que solicita es el cliente
            $request->input('ride_id'),
			"Detalle del pago", 
			$ride->parent->payment_method_id,      //payment_method_id
			$invoice_name,
            $invoice_number,
            1,
            1,
            1,
            $userCobra->app_key
		);
        return $response;
       
        } catch (\Throwable $th) {
            echo $th;
            die;
            return ['status'=>false, 'message'=>'Ocurrio un error en el server. '. $th->getMessage()];
        }
	}
	

}

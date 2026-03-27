<?php

// --- RUTAS PÚBLICAS PASAJERO ---
Route::group(['prefix'=>'v1'], function(){
	Route::post('users/sign-in', 'Auth\AuthenticateController@authenticate'); 
	
	Route::get('payment-methods', function(){
		return response()->json(['status'=>true, 'item'=>[]]);
	});

	Route::get('type-requests', function(){
		return response()->json(['status'=>true, 'data'=>[]]);
	});

	Route::get('organizations', function(){
		return response()->json(['status'=>true, 'data'=>[]]);
	});

	// OTP se maneja por los endpoints reales en api-auth/
	Route::post('users/resend-code', 'Auth\AuthenticateController@sendOtpPhone');

	Route::post('users/validate-otp', 'Auth\AuthenticateController@verifyOtpPhone');

	Route::get('users/profile', function(){
		try {
			$user = \JWTAuth::parseToken()->authenticate();
			return response()->json(['status'=>true, 'data'=>[
				'id' => $user->id,
				'name' => $user->name,
				'email' => $user->email,
				'cellphone' => $user->cellphone,
			]]);
		} catch (\Exception $e) {
			return response()->json(['status'=>false, 'message'=>'No autorizado.'], 401);
		}
	});
});

//   --- RESTO DE RUTAS EXISTENTES ---
//Route::post('api/authenticate', 'Auth\AuthenticateController@authenticate');
Route::group(['prefix' => 'api-auth'], function(){
    Route::post('authenticate', 'Auth\AuthenticateController@authenticate');
    Route::post('register', 'Auth\AuthenticateController@register');

	Route::post('send-code-email', 'Auth\AuthenticateController@sendCodeEmail');
	Route::post('valid-code', 'Auth\AuthenticateController@validCode');
	Route::post('recover-password', 'Auth\AuthenticateController@recoverPassword');

	// Endpoints para registro de PASAJERO con teléfono (rate limited)
	Route::group(['middleware' => ['throttle:5,1']], function(){
		Route::post('send-otp-phone', 'Auth\AuthenticateController@sendOtpPhone');
		Route::post('verify-otp-phone', 'Auth\AuthenticateController@verifyOtpPhone');
	});
	Route::post('register-with-phone', 'Auth\AuthenticateController@registerWithPhone');

	// Endpoints para registro de CONDUCTOR con teléfono (rate limited)
	Route::group(['middleware' => ['throttle:5,1']], function(){
		Route::post('send-otp-phone-driver', 'Auth\AuthenticateController@sendOtpPhoneDriver');
		Route::post('verify-otp-phone-driver', 'Auth\AuthenticateController@verifyOtpPhoneDriver');
	});
	Route::post('register-driver-with-phone', 'Auth\AuthenticateController@registerDriverWithPhone');

	// Endpoints para LOGIN de PASAJERO con teléfono
	Route::post('login-with-phone', 'Auth\AuthenticateController@loginWithPhone');
	Route::post('login-verify-phone', 'Auth\AuthenticateController@loginVerifyPhone');

	// Endpoints para LOGIN de CONDUCTOR con teléfono
	Route::post('login-with-phone-driver', 'Auth\AuthenticateController@loginWithPhoneDriver');
	Route::post('login-verify-phone-driver', 'Auth\AuthenticateController@loginVerifyPhoneDriver');

	// Endpoints para obtener marcas y modelos (Sueltos porque la App no envía token aquí)
	Route::get('vehicle-brands', 'Auth\AuthenticateController@getVehicleBrands');
	Route::get('vehicle-models', 'Auth\AuthenticateController@getVehicleModels');

	// Endpoints que requieren autenticación
	Route::group(['middleware' => ['jwt.auth']], function(){
		Route::post('register-vehicle', 'Auth\AuthenticateController@registerDriverVehicle');
		Route::post('update-facial-photo', 'Auth\AuthenticateController@updateFacialPhoto');

		// Endpoint para APROBAR CONDUCTOR (Admin - requiere autenticación)
		Route::post('approve-driver', 'Auth\AuthenticateController@approveDriver');
	});

});

// Cambiamos el grupo de v1 a rutas estándar de Laravel para mayor confiabilidad en el servidor AWS
Route::group(['prefix'=>'v1', 'middleware' => ['jwt.auth'], 'namespace'=>'App\\Http\\Controllers\\Api'], function(){
	// Historial de pasajero (stub de compatibilidad para web/mobile)
	Route::get('users/history', function(){
		return response()->json([
			'status' => true,
			'data' => ['histories' => []],
		]);
	});

	// Dashboard
	Route::get('update-dashboard', 'DashboardController@getUpdateaDashboard');
	// App
	Route::post('login', 'AppController@postLogin');
	Route::get('check-login', 'AppController@getCheckLogin');
	Route::get('nfc-tags', 'AppController@getNfcTags'); // Descargar NFCs
	Route::post('register-assistance', 'AppController@postRegisterAssistance'); // Registrar Asistencia
	Route::post('generate-payment', 'AppController@postGeneratePayment'); // Descargar NFCs

	// Perfil de conductor (v1/drivers/{id})
	Route::get('drivers/{id}', function($id){
		try {
			// Usamos DB::table para evitar el error de count() en Eloquent Builder
			$driver = \DB::table('drivers')->where('id', $id)->orWhere('user_id', $id)->first();
			
			if(!$driver){
				return response()->json(['status'=>false, 'message'=>'No se encontró el perfil de conductor.'], 404);
			}

			$data = [
				'drivers_id' => $driver->id,
				'user_id' => $driver->user_id,
				'user_name' => $driver->first_name . ' ' . $driver->last_name,
				'user_first_name' => $driver->first_name,
				'user_last_name' => $driver->last_name,
				'user_cellphone' => $driver->cellphone,
				'drivers_license_number' => $driver->license_number,
				'drivers_car_with_grill' => (int)$driver->car_with_grill,
				'drivers_travel_with_pets' => (int)$driver->travel_with_pets,
				'driver_rating_total' => '5.0', 
				'rides_total' => '0',
				'drivers_image' => (strpos($driver->image, 'http') === 0) ? $driver->image : url($driver->image),
				'qr_image' => $driver->qr_image,
			];
			
			// Verificamos vehículos usando DB::table
			$vehicles = [];
			$dvRel = \DB::table('driver_vehicles')->where('parent_id', $driver->id)->where('active', 1)->get();
			foreach($dvRel as $dv){
				$brand = \DB::table('vehicle_brands')->where('id', $dv->vehicle_brand_id)->first();
				$model = \DB::table('vehicle_models')->where('id', $dv->vehicle_model_id)->first();
				
				$brName = $brand ? $brand->name : '';
				$moName = $model ? $model->name : '';
				
				$vehicles[] = [
					'id' => $dv->id,
					'vehicle_name' => trim($brName . ' ' . $moName) ?: 'Vehículo',
					'plate' => $dv->number_plate ?: '',
				];
			}
			$data['driver_vehicles'] = $vehicles;
			
			return response()->json(['status'=>true, 'data'=>[$data]]);
		} catch (\Exception $e) {
			\Log::error("Error en v1/drivers/{id}: ".$e->getMessage());
			return response()->json(['status'=>false, 'message'=>'Error interno del servidor.'], 500);
		}
	});

    // Actualizar estado del conductor (Disponible/Apagado)
    Route::put('drivers/active', function(\Illuminate\Http\Request $request){
        try {
			$user = auth()->user();
			if(!$user){
				try {
					$user = \JWTAuth::parseToken()->authenticate();
				} catch (\Exception $e_jwt) {
					return response()->json(['status'=>false, 'message'=>'No autenticado.'], 401);
				}
			}
			
			$driver = \DB::table('drivers')->where('user_id', $user->id)->first();
			if(!$driver){
				return response()->json(['status'=>false, 'message'=>'No se encontró el perfil de conductor.'], 404);
			}
			
			$activeInput = $request->input('active');
			$active = ($activeInput || $activeInput === 'true' || $activeInput == 1) ? 1 : 0;
			
			$updateData = ['active' => $active];
			if($request->has('latitude')) $updateData['latitude'] = $request->input('latitude');
			if($request->has('longitude')) $updateData['longitude'] = $request->input('longitude');
			
			\DB::table('drivers')->where('id', $driver->id)->update($updateData);
			
			return response()->json(['status'=>true, 'message'=>'Estado actualizado correctamente.', 'active' => (int)$active]);
		} catch (\Exception $e) {
			\Log::error("Error en v1/drivers/active: ".$e->getMessage());
			return response()->json(['status'=>false, 'message'=>'Error interno del servidor.'], 500);
		}
    });

    // Solicitudes disponibles (Stub para evitar 404)
    Route::get('driver-requests/available', function(){
        return response()->json(['status'=>true, 'item'=>[]]);
    });

    Route::get('drivers/history', function(){
        return response()->json(['status'=>true, 'data'=>[]]);
    });

    Route::get('drivers/payment-methods', function(){
        return response()->json(['status'=>true, 'data'=>[]]);
    });

});

// --- FIN ---
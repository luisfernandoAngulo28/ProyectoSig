<?php

//Route::post('api/authenticate', 'Auth\AuthenticateController@authenticate');
Route::group(['prefix' => 'api-auth'], function(){
    Route::post('authenticate', 'Auth\AuthenticateController@authenticate');
    Route::post('register', 'Auth\AuthenticateController@register');

	Route::post('send-code-email', 'Auth\AuthenticateController@sendCodeEmail');
	Route::post('valid-code', 'Auth\AuthenticateController@validCode');
	Route::post('recover-password', 'Auth\AuthenticateController@recoverPassword');

	// Endpoints para registro de PASAJERO con teléfono
	Route::post('send-otp-phone', 'Auth\AuthenticateController@sendOtpPhone');
	Route::post('verify-otp-phone', 'Auth\AuthenticateController@verifyOtpPhone');
	Route::post('register-with-phone', 'Auth\AuthenticateController@registerWithPhone');

	// Endpoints para registro de CONDUCTOR con teléfono
	Route::post('send-otp-phone-driver', 'Auth\AuthenticateController@sendOtpPhoneDriver');
	Route::post('verify-otp-phone-driver', 'Auth\AuthenticateController@verifyOtpPhoneDriver');
	Route::post('register-driver-with-phone', 'Auth\AuthenticateController@registerDriverWithPhone');

	// Endpoints para LOGIN de PASAJERO con teléfono
	Route::post('login-with-phone', 'Auth\AuthenticateController@loginWithPhone');
	Route::post('login-verify-phone', 'Auth\AuthenticateController@loginVerifyPhone');

	// Endpoints para LOGIN de CONDUCTOR con teléfono
	Route::post('login-with-phone-driver', 'Auth\AuthenticateController@loginWithPhoneDriver');
	Route::post('login-verify-phone-driver', 'Auth\AuthenticateController@loginVerifyPhoneDriver');

	// Endpoints que requieren autenticación
	Route::group(['middleware' => ['jwt.auth']], function(){
		Route::get('vehicle-brands', 'Auth\AuthenticateController@getVehicleBrands');
		Route::get('vehicle-models', 'Auth\AuthenticateController@getVehicleModels');
		Route::post('register-vehicle', 'Auth\AuthenticateController@registerDriverVehicle');
		Route::post('update-facial-photo', 'Auth\AuthenticateController@updateFacialPhoto');
	});

	// Endpoint para APROBAR CONDUCTOR (Admin)
	Route::post('approve-driver', 'Auth\AuthenticateController@approveDriver');

});

// Cambiamos el grupo de v1 a rutas estándar de Laravel para mayor confiabilidad en el servidor AWS
Route::group(['prefix'=>'v1', 'middleware' => ['jwt.auth'], 'namespace'=>'App\\Http\\Controllers\\Api'], function(){
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
			$driver = \App\Driver::where('user_id', $id)->first();
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
				'drivers_image' => $driver->image,
				'qr_image' => $driver->qr_image,
			];
			
			// Verificamos relación de vehículos
			$vehicles = [];
			try {
				$dvRel = $driver->driver_vehicles; // Usamos la relación correcta
				if($dvRel){
					foreach($dvRel as $dv){
						$brand = $dv->vehicle_brand ? $dv->vehicle_brand->name : '';
						$model = $dv->vehicle_model ? $dv->vehicle_model->name : '';
						$vehicles[] = [
							'id' => $dv->id,
							'vehicle_name' => trim($brand . ' ' . $model) ?: 'Vehículo',
							'plate' => $dv->number_plate ?: '',
						];
					}
				}
			} catch (\Exception $e_veh) {
				\Log::info("Error obteniendo vehículos: ".$e_veh->getMessage());
			}
			$data['driver_vehicles'] = $vehicles;
			
			return response()->json(['status'=>true, 'data'=>[$data]]);
		} catch (\Exception $e) {
			return response()->json(['status'=>false, 'message'=>'Error obteniendo perfil: '.$e->getMessage()], 500);
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
			
			$driver = \App\Driver::where('user_id', $user->id)->first();
			if(!$driver){
				return response()->json(['status'=>false, 'message'=>'No se encontró el perfil de conductor.'], 404);
			}
			
			$driver->active = ($request->input('active') || $request->input('active') === 'true' || $request->input('active') === 1) ? 1 : 0;
			if($request->has('latitude')) $driver->latitude = $request->input('latitude');
			if($request->has('longitude')) $driver->longitude = $request->input('longitude');
			$driver->save();
			
			return response()->json(['status'=>true, 'message'=>'Estado actualizado correctamente.', 'active' => $driver->active]);
		} catch (\Exception $e) {
			return response()->json(['status'=>false, 'message'=>'Error de servidor: '.$e->getMessage()], 500);
		}
    });

    // Solicitudes disponibles (Stub para evitar 404)
    Route::get('driver-requests/available', function(){
        return response()->json(['status'=>true, 'item'=>[]]);
    });

});
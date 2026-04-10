<?php

// --- RUTAS PÚBLICAS PASAJERO ---
Route::group(['prefix'=>'v1'], function(){
	Route::post('users/sign-in', 'Auth\AuthenticateController@authenticate'); 
	
	Route::get('payment-methods', function(){
		return response()->json(['status'=>true, 'item'=>[]]);
	});

	Route::get('type-requests', function(){
		$fallback = [
			[
				'type_requests_id' => 1,
				'nameForFront' => 'Viaje por Taxi',
				'logo' => null,
				'description' => 'Solicita transporte punto a punto',
				'text_color' => '#00CFC8',
				'active' => 1,
			],
			[
				'type_requests_id' => 2,
				'nameForFront' => 'Viaje por Mototaxi',
				'logo' => null,
				'description' => 'Traslado rapido en mototaxi',
				'text_color' => '#00CFC8',
				'active' => 1,
			],
			[
				'type_requests_id' => 3,
				'nameForFront' => 'Envio',
				'logo' => null,
				'description' => 'Envia paquetes y documentos',
				'text_color' => '#00CFC8',
				'active' => 1,
			],
		];

		try {
			$items = \DB::table('type_requests')
				->orderBy('id', 'asc')
				->get();

			$data = [];
			foreach ($items as $item) {
				$data[] = [
					'type_requests_id' => (int)$item->id,
					'nameForFront' => (string)($item->name ?: ''),
					'logo' => $item->logo_image,
					'description' => (string)($item->description ?: ''),
					'text_color' => (string)($item->text_color ?: '#000000'),
					'active' => 1,
				];
			}

			if (empty($data)) {
				return response()->json(['status' => true, 'data' => $fallback]);
			}

			return response()->json(['status' => true, 'data' => $data]);
		} catch (\Exception $e) {
			\Log::error('Error v1/type-requests: '.$e->getMessage());
			return response()->json(['status' => true, 'data' => $fallback]);
		}
	});

	Route::get('organizations', function(){
		return response()->json(['status'=>true, 'data'=>[]]);
	});

	// OTP se maneja por los endpoints reales en api-auth/
	Route::post('users/resend-code', 'Auth\AuthenticateController@sendOtpPhone');

	// Compatibilidad: flujo antiguo (otp + user_id) y flujo nuevo por telefono.
	Route::post('users/validate-otp', function(\Illuminate\Http\Request $request){
		$otp = trim((string)$request->input('otp', ''));
		$userId = (int)$request->input('user_id', 0);

		if ($otp !== '' && $userId > 0) {
			try {
				$otpRecord = \DB::table('otps')
					->where('parent_id', $userId)
					->where('code', $otp)
					->orderBy('id', 'desc')
					->first();

				if (!$otpRecord) {
					return response()->json([
						'status' => false,
						'message' => ['El codigo es invalido.'],
						'errors' => ['OTP invalido'],
					], 400);
				}

				if ((int)$otpRecord->time_expiration_code > 0 && time() > (int)$otpRecord->time_expiration_code) {
					return response()->json([
						'status' => false,
						'message' => ['El codigo ya expiro.'],
						'errors' => ['OTP expirado'],
					], 400);
				}

				$user = \App\User::find($userId);
				if (!$user) {
					return response()->json([
						'status' => false,
						'message' => ['Usuario no encontrado.'],
						'errors' => ['Usuario invalido'],
					], 404);
				}

				$updates = [];
				if (\Schema::hasColumn('users', 'verified')) {
					$updates['verified'] = 1;
				}
				if (\Schema::hasColumn('users', 'is_verify')) {
					$updates['is_verify'] = 1;
				}
				if (!empty($updates)) {
					\DB::table('users')->where('id', $userId)->update($updates);
				}

				$user = \App\User::find($userId);
				$token = \JWTAuth::fromUser($user);

				return response()->json([
					'status' => true,
					'message' => ['Codigo verificado correctamente.'],
					'errors' => [],
					'data' => [
						'token' => $token,
						'expirationDate' => date('d/n/Y H:i:s', strtotime('+6 months')),
						'id' => (int)$user->id,
						'name' => (string)($user->name ?: ''),
						'first_name' => (string)($user->first_name ?: $user->name),
						'last_name' => (string)($user->last_name ?: ''),
						'email' => (string)($user->email ?: ''),
						'cellphone' => (string)($user->cellphone ?: ''),
						'code_cellphone' => (string)($user->code_cellphone ?: '+591'),
						'address' => (string)($user->address ?: ''),
						'is_verify' => true,
						'client_socket_code' => (string)($user->client_socket_code ?: ''),
					],
				], 200);
			} catch (\Exception $e) {
				\Log::error('Error v1/users/validate-otp legacy: '.$e->getMessage());
				return response()->json([
					'status' => false,
					'message' => ['Error en el servidor.'],
					'errors' => ['Error interno'],
				], 500);
			}
		}

		$controller = app('App\\Http\\Controllers\\Auth\\AuthenticateController');
		return $controller->verifyOtpPhone($request);
	});

	// Compatibilidad con app pasajero: registro por email/telefono.
	Route::post('users/sign-up', function(\Illuminate\Http\Request $request){
		$validator = \Validator::make($request->all(), [
			'name' => 'required|min:2',
			'email' => 'required|email|unique:users,email',
			'cellphone' => 'required|digits_between:8,9|unique:users,cellphone',
			'password' => 'required|min:6',
		]);

		if ($validator->fails()) {
			return response()->json([
				'status' => false,
				'message' => ['Debes enviar los parametros correctamente.'],
				'errors' => $validator->errors()->all(),
			], 400);
		}

		try {
			$user = new \App\User;
			$user->name = trim((string)$request->input('name', ''));
			$user->email = strtolower(trim((string)$request->input('email', '')));
			$user->cellphone = preg_replace('/\D+/', '', (string)$request->input('cellphone', ''));
			$user->password = bcrypt((string)$request->input('password', ''));

			if (\Schema::hasColumn('users', 'first_name')) {
				$user->first_name = (string)$request->input('first_name', $user->name);
			}
			if (\Schema::hasColumn('users', 'last_name')) {
				$user->last_name = (string)$request->input('last_name', '');
			}
			if (\Schema::hasColumn('users', 'gender')) {
				$user->gender = (string)$request->input('gender', 'male');
			}
			if (\Schema::hasColumn('users', 'sex')) {
				$user->sex = (string)$request->input('gender', 'male');
			}
			if (\Schema::hasColumn('users', 'type')) {
				$user->type = 'customer';
			}
			if (\Schema::hasColumn('users', 'active')) {
				$user->active = 1;
			}
			if (\Schema::hasColumn('users', 'verified')) {
				$user->verified = 0;
			}
			if (\Schema::hasColumn('users', 'is_verify')) {
				$user->is_verify = 0;
			}
			if (\Schema::hasColumn('users', 'client_socket_code')) {
				$user->client_socket_code = (string)$request->input('client_socket_code', '');
			}

			if ($request->hasFile('image')) {
				$path = $request->file('image')->store('users', 'public');
				if (\Schema::hasColumn('users', 'image')) {
					$user->image = $path;
				}
			} else {
				$imageStr = (string)$request->input('image', '');
				if ($imageStr !== '' && \Schema::hasColumn('users', 'image')) {
					$user->image = $imageStr;
				}
			}

			$user->save();

			$otpCode = (string)rand(100000, 999999);
			$otpInsert = [
				'parent_id' => $user->id,
				'code' => $otpCode,
				'time_expiration_code' => time() + 3600,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			];

			// Compatibilidad con esquemas legacy de la tabla otps.
			if (\Schema::hasColumn('otps', 'type')) {
				$otpInsert['type'] = 'email';
			}
			if (\Schema::hasColumn('otps', 'phone')) {
				$otpInsert['phone'] = (string)$user->cellphone;
			}
			if (!\Schema::hasColumn('otps', 'created_at')) {
				unset($otpInsert['created_at']);
			}
			if (!\Schema::hasColumn('otps', 'updated_at')) {
				unset($otpInsert['updated_at']);
			}

			\DB::table('otps')->insert($otpInsert);

			return response()->json([
				'status' => true,
				'message' => ['Usuario registrado correctamente.'],
				'errors' => [],
				'data' => [
					'user' => [
						'id' => (int)$user->id,
						'name' => (string)$user->name,
						'first_name' => (string)($user->first_name ?: $user->name),
						'last_name' => (string)($user->last_name ?: ''),
						'email' => (string)$user->email,
						'cellphone' => (string)$user->cellphone,
						'address' => (string)($user->address ?: ''),
						'is_verify' => false,
						'image' => $user->image,
						'client_socket_code' => (string)($user->client_socket_code ?: ''),
					],
					'code' => $otpCode,
				],
			], 201);
		} catch (\Exception $e) {
			\Log::error('Error v1/users/sign-up: '.$e->getMessage());
			return response()->json([
				'status' => false,
				'message' => ['Error en el servidor.'],
				'errors' => ['Error interno'],
			], 500);
		}
	});

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

	// ============================================================
	// Endpoint para subir QR de cobros del conductor
	// POST /api-auth/upload-driver-qr
	// ============================================================
	Route::group(['middleware' => ['jwt.auth']], function(){
		Route::post('upload-driver-qr', function(\Illuminate\Http\Request $request){
			try {
				// Obtener conductor autenticado
				$user = \JWTAuth::parseToken()->authenticate();
				if (!$user) {
					return response()->json(['status' => false, 'message' => 'No autenticado'], 401);
				}

				// Buscar el registro de conductor
				$driver = \App\Driver::where('user_id', $user->id)
					->orWhere('id', $user->id)
					->first();

				if (!$driver) {
					return response()->json(['status' => false, 'message' => 'Conductor no encontrado'], 404);
				}

				if (!$request->hasFile('qr_image')) {
					return response()->json(['status' => false, 'message' => 'No se recibió ninguna imagen'], 400);
				}

				$file = $request->file('qr_image');

				// Validar que sea imagen
				if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
					return response()->json(['status' => false, 'message' => 'El archivo debe ser una imagen (jpg, png, gif)'], 400);
				}

				// Crear carpeta si no existe
				$qrDir = public_path('drivers/qr');
				if (!file_exists($qrDir)) {
					mkdir($qrDir, 0755, true);
				}

				// Eliminar QR anterior si existe
				if ($driver->qr_image) {
					$oldPath = public_path($driver->qr_image);
					if (file_exists($oldPath)) {
						@unlink($oldPath);
					}
				}

				// Guardar nueva imagen
				$ext = $file->getClientOriginalExtension() ?: 'png';
				$fileName = 'qr_' . $driver->id . '_' . time() . '.' . $ext;
				$file->move($qrDir, $fileName);
				$relativePath = 'drivers/qr/' . $fileName;

				// Actualizar en base de datos
				\DB::table('drivers')->where('id', $driver->id)->update([
					'qr_image' => $relativePath,
					'updated_at' => date('Y-m-d H:i:s'),
				]);

				\Log::info("QR actualizado: driver {$driver->id} → {$relativePath}");

				return response()->json([
					'status' => true,
					'message' => 'Código QR actualizado exitosamente',
					'data' => [
						'qr_image' => url($relativePath),
					]
				], 200);

			} catch (\Exception $e) {
				\Log::error('Error en upload-driver-qr: ' . $e->getMessage());
				return response()->json(['status' => false, 'message' => 'Error al subir el QR: ' . $e->getMessage()], 500);
			}
		});
	}); // fin grupo jwt.auth upload-driver-qr

	// ============================================================
	// Endpoint para subir FOTO DE PERFIL del conductor
	// POST /api-auth/upload-driver-photo
	// ============================================================
	Route::group(['middleware' => ['jwt.auth']], function(){
		Route::post('upload-driver-photo', function(\Illuminate\Http\Request $request){
			try {
				$user = \JWTAuth::parseToken()->authenticate();
				if (!$user) {
					return response()->json(['status' => false, 'message' => 'No autenticado'], 401);
				}

				$driver = \App\Driver::where('user_id', $user->id)->orWhere('id', $user->id)->first();
				if (!$driver) {
					return response()->json(['status' => false, 'message' => 'Conductor no encontrado'], 404);
				}

				if (!$request->hasFile('driver_photo')) {
					return response()->json(['status' => false, 'message' => 'No se recibió ninguna imagen'], 400);
				}

				$file = $request->file('driver_photo');

				if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
					return response()->json(['status' => false, 'message' => 'El archivo debe ser una imagen'], 400);
				}

				// Crear directorio si no existe
				$photoDir = public_path('drivers/photos');
				if (!file_exists($photoDir)) {
					mkdir($photoDir, 0755, true);
				}

				// Eliminar foto anterior
				if ($driver->image && file_exists(public_path($driver->image))) {
					@unlink(public_path($driver->image));
				}

				$ext = $file->getClientOriginalExtension() ?: 'jpg';
				$fileName = 'photo_' . $driver->id . '_' . time() . '.' . $ext;
				$file->move($photoDir, $fileName);
				$relativePath = 'drivers/photos/' . $fileName;

				\DB::table('drivers')->where('id', $driver->id)->update([
					'image'      => $relativePath,
					'updated_at' => date('Y-m-d H:i:s'),
				]);

				\Log::info("Foto actualizada: driver {$driver->id} → {$relativePath}");

				return response()->json([
					'status'  => true,
					'message' => 'Foto de perfil actualizada exitosamente',
					'data'    => [
						'drivers_image' => url($relativePath),
					]
				], 200);

			} catch (\Exception $e) {
				\Log::error('Error en upload-driver-photo: ' . $e->getMessage());
				return response()->json(['status' => false, 'message' => 'Error al subir la foto'], 500);
			}
		});
	}); // fin grupo jwt.auth upload-driver-photo

	// Endpoints de catálogos para dropdowns (públicos, sin autenticación)
	Route::get('regions',       'Auth\AuthenticateController@getRegions');
	Route::get('cities',        'Auth\AuthenticateController@getCitiesByRegion');
	Route::get('organizations', 'Auth\AuthenticateController@getOrganizationsByCity');

});

// Cambiamos el grupo de v1 a rutas estándar de Laravel para mayor confiabilidad en el servidor AWS
Route::group(['prefix'=>'v1', 'middleware' => ['jwt.auth'], 'namespace'=>'Api'], function(){
	// Historial de pasajero (stub de compatibilidad para web/mobile)
	Route::get('users/history', function(){
		return response()->json([
			'status' => true,
			'data' => ['histories' => []],
		]);
	});

	// Dashboard
	Route::get('update-dashboard', function(){
		return response()->json([
			'status' => true,
			'message' => 'Dashboard endpoint temporalmente deshabilitado',
			'item' => null,
		]);
	});

	// ==================== SOPORTE PARA SOLICITUD DE VIAJE (PASAJERO) ====================
	Route::post('rates', function(\Illuminate\Http\Request $request){
		try {
			$distanceRaw = (string)$request->input('distance', '1');
			$distance = (float)str_replace([',', ' km', ' m'], ['.', '', ''], strtolower($distanceRaw));
			if ($distance <= 0) {
				$distance = 1;
			}

			$cityId = (int)$request->input('city', 0);
			$rate = null;
			if ($cityId > 0) {
				$rate = \DB::table('rates')->where('city_id', $cityId)->orderBy('id', 'desc')->first();
			}

			if (!$rate) {
				$rate = \DB::table('rates')->orderBy('id', 'desc')->first();
			}

			if ($rate) {
				$baseRate = (float)$rate->base_rate;
				$kmRate = isset($rate->km_rate) ? (float)$rate->km_rate : 0.0;
				$total = $baseRate + ($kmRate * $distance);
				if ($total <= 0) {
					$total = 10.0;
				}

				return response()->json([
					'status' => true,
					'data' => [
						'base_rate' => round($total, 2),
					]
				]);
			}

			// Fallback seguro si no hay tabla rates poblada
			return response()->json([
				'status' => true,
				'data' => [
					'base_rate' => round(max(10.0, 5 + ($distance * 2.0)), 2),
				]
			]);
		} catch (\Exception $e) {
			\Log::error('Error v1/rates: '.$e->getMessage());
			return response()->json([
				'status' => true,
				'data' => [
					'base_rate' => 10.0,
				]
			]);
		}
	});

	Route::get('cities/lat-lng', function(\Illuminate\Http\Request $request){
		try {
			$lat = $request->input('lat');
			$lng = $request->input('long');

			$city = \DB::table('cities')->where('active', 1)->orderBy('id', 'asc')->first();
			if (!$city) {
				$city = \DB::table('cities')->orderBy('id', 'asc')->first();
			}

			if ($city) {
				$cityName = isset($city->name) ? (string)$city->name : 'Ciudad';
				return response()->json([
					'status' => true,
					'item' => [
						'id' => (int)$city->id,
						'name' => $cityName,
						'lat' => $lat,
						'lng' => $lng,
					]
				]);
			}

			return response()->json([
				'status' => true,
				'item' => [
					'id' => 1,
					'name' => 'Santa Cruz',
					'lat' => $lat,
					'lng' => $lng,
				]
			]);
		} catch (\Exception $e) {
			\Log::error('Error v1/cities/lat-lng: '.$e->getMessage());
			return response()->json([
				'status' => true,
				'item' => [
					'id' => 1,
					'name' => 'Santa Cruz',
				]
			]);
		}
	});

	Route::get('drivers/nearby', function(\Illuminate\Http\Request $request){
		try {
			$latitude  = (float) $request->input('latitude',  -17.7833);
			$longitude = (float) $request->input('longitude', -63.1821);
			$distance  = (int)   $request->input('distance',  5);

			// Obtener conductores activos con su primer vehículo (para type_vehicle)
			$rawDrivers = \DB::table('drivers as d')
				->leftJoin('vehicles as v', function ($join) {
					$join->on('v.driver_id', '=', 'd.id')
						 ->whereRaw('v.id = (SELECT MIN(v2.id) FROM vehicles v2 WHERE v2.driver_id = d.id)');
				})
				->leftJoin('users as u', 'u.id', '=', 'd.user_id')
				->where('d.active', 1)
				->whereNotNull('d.latitude')
				->whereNotNull('d.longitude')
				->select(
					'd.id',
					'd.user_id as userId',
					'd.movil_number as movilNumber',
					'd.image',
					'd.latitude',
					'd.longitude',
					'd.active',
					'd.organization_id as organizationId',
					'd.qr_image as qrImage',
					'd.uuid',
					'd.created_at as createdAt',
					'd.updated_at as updatedAt',
					\DB::raw('1 as isActiveForCareer'),
					\DB::raw("COALESCE(v.type_vehicle, '') as type_vehicle")
				)
				->orderBy('d.id', 'desc')
				->limit(30)
				->get();

			$drivers = [];
			foreach ($rawDrivers as $d) {
				// Filtrar por distancia si se proporcionaron coordenadas
				$driverLat = (float) $d->latitude;
				$driverLng = (float) $d->longitude;
				$earthRadius = 6371; // km
				$latDiff = deg2rad($driverLat - $latitude);
				$lngDiff = deg2rad($driverLng - $longitude);
				$a = sin($latDiff/2) * sin($latDiff/2) +
					cos(deg2rad($latitude)) * cos(deg2rad($driverLat)) *
					sin($lngDiff/2) * sin($lngDiff/2);
				$distKm = $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));

				if ($distKm > $distance) continue; // fuera del radio

				$drivers[] = [
					'id'               => (int)    $d->id,
					'userId'           => (int)    $d->userId,
					'movilNumber'      => (int)    $d->movilNumber,
					'image'            => (string) ($d->image ?? ''),
					'latitude'         => (string) $d->latitude,
					'longitude'        => (string) $d->longitude,
					'active'           => (int)    $d->active,
					'organizationId'   => (int)    ($d->organizationId ?? 0),
					'qrImage'          => $d->qrImage,
					'uuid'             => (string) ($d->uuid ?? ''),
					'isActiveForCareer'=> true,
					'type_vehicle'     => (string) ($d->type_vehicle ?? ''), // ✅ Para marker emoji
					'createdAt'        => $d->createdAt ?? now()->toISOString(),
					'updatedAt'        => $d->updatedAt ?? now()->toISOString(),
				];
			}

			return response()->json([
				'status' => true,
				'item'   => ['drivers' => $drivers],
			]);

		} catch (\Exception $e) {
			\Log::error('Error v1/drivers/nearby: '.$e->getMessage());
			return response()->json([
				'status' => true,
				'item'   => ['drivers' => []],
			]);
		}
	});

	// ==================== FIN SOPORTE PASAJERO ====================

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
				'qr_image' => $driver->qr_image
                    ? ((strpos($driver->qr_image, 'http') === 0) ? $driver->qr_image : url($driver->qr_image))
                    : null,
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

    // ===== ENDPOINTS DE SOLICITUD DE VIAJES (PASAJERO) =====
    // POST v1/type-requests/{homeId}/requests-trip
    Route::post('type-requests/{homeId}/requests-trip', 'TripRequestController@createTripRequest');

    // PUT v1/requests/cancel - Cancelar solicitudes pendientes
    Route::put('requests/cancel', 'TripRequestController@cancelPendingRequests');

    // GET v1/requests/ride/user/available - Obtener viajes activos del usuario
    Route::get('requests/ride/user/available', 'TripRequestController@getAvailableRides');

    // GET v1/requests/{id} - Obtener detalles de una solicitud
    Route::get('requests/{id}', 'TripRequestController@getTripRequest');

});

// --- FIN ---
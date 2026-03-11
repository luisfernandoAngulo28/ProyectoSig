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

	// Endpoints para REGISTRO DE VEHÍCULO de conductor
	Route::get('vehicle-brands', 'Auth\AuthenticateController@getVehicleBrands');
	Route::get('vehicle-models', 'Auth\AuthenticateController@getVehicleModels');
	Route::post('register-vehicle', 'Auth\AuthenticateController@registerDriverVehicle');

	// Endpoint para APROBAR CONDUCTOR (Admin)
	Route::post('approve-driver', 'Auth\AuthenticateController@approveDriver');

	// Endpoint para ACTUALIZACIÓN MENSUAL DE FOTO FACIAL
	Route::post('update-facial-photo', 'Auth\AuthenticateController@updateFacialPhoto');

});

app('api.router')->group(['version'=>'v1', 'namespace'=>'App\\Http\\Controllers\\Api'], function($api){
	// Dashboard
	$api->get('update-dashboard', 'DashboardController@getUpdateaDashboard');
	// App
	$api->post('login', 'AppController@postLogin');
	$api->get('check-login', 'AppController@getCheckLogin');
	$api->get('nfc-tags', 'AppController@getNfcTags'); // Descargar NFCs
	$api->post('register-assistance', 'AppController@postRegisterAssistance'); // Registrar Asistencia
	$api->post('generate-payment', 'AppController@postGeneratePayment'); // Descargar NFCs

});
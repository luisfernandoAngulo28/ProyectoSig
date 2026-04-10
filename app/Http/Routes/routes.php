<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/', 'MainController@showIndex');
Route::get('send-sms', 'MainController@getSendSms');

Route::get('auth/instagram', 'Auth\AuthController@redirectToProvider');
Route::get('auth/instagram/callback', 'Auth\AuthController@handleProviderCallback');

Route::group(['prefix'=>'process'], function(){
    Route::get('/change-locale/{lang}', 'ProcessController@getChangeLocale');
    Route::post('/save-model', 'ProcessController@postSaveModel');
    Route::post('/product-bridge-search-own', 'ProcessController@postProductBridgeSearch');
    Route::post('/model', 'ProcessController@postModel');    
    Route::post('/formulario', 'ProcessController@postFormulario');
    Route::post('/update-cart-two', 'ProcessController@postUpdateCartTwo');
    Route::get('/finalizar-compra-t/{cart_id?}', 'ProcessController@getFinishSale');
    Route::post('/finish-sale-t', 'ProcessController@postFinishSale');

    Route::post('/ajax-fill-models-by-brand', 'ProcessController@ajaxFillModelsByBrand');
});

Route::group(['prefix'=>'payments'], function(){
    Route::get('/todotix/make-payment/{id}', 'Payment\TodotixController@getMakePayment');
    Route::get('/todotix/make-payment-for-all', 'Payment\TodotixController@getMakePaymentForAll');
});

Route::group(['prefix'=>'custom-admin'], function(){
    Route::get('/', 'CustomAdminController@getIndex');
});


Route::group(['prefix'=>'customer-admin'], function(){
    Route::get('reportes-totales/{view?}', 'CustomAdminController@reportTotalesDrivers');
});





Route::group(['prefix'=>'customer-admin'], function(){
    Route::get('reportes-totales-request/{view?}', 'CustomAdminController@reportOrganizationTotalRequest');
});

Route::group(['prefix'=>'customer-admin'], function(){
    Route::get('reportes-amount-user/{view?}', 'CustomAdminController@reportUserAmount');
});

Route::group(['prefix'=>'customer-admin'], function(){
    Route::get('find-request/{uuid}', 'MainController@getPointsRide');
    Route::get('register-driver', 'MainController@registerDriver');
    Route::get('/register-driver/step2/{driverId}', 'MainController@registerVehicle');
    Route::get('/register-driver/step3', 'MainController@registerSuccess');
    Route::post('/register-driver/step1', 'MainController@postRegisterDriver');
    Route::post('/register-driver/step2', 'MainController@postRegisterVehicle');
    Route::get('/verify/{userId}', 'MainController@verifyUser');
    Route::get('/models-by-brand/{brandId}', 'MainController@modelByBrand');
    Route::get('/brands-by-type/{type}', 'MainController@brandsByType');
    
    Route::get('/delete-driver/{driverId}', 'MainController@deleteDriver');

    Route::get('/register-driver/step1-update/{id}', 'MainController@updateRegisterDriver');
    Route::post('/register-driver/update/step1', 'MainController@updateDriver');
    Route::get('device-code', 'MainController@driverDevices');
    Route::get('delete-code/{id}', 'MainController@deleteDeviceCodes');
    
    Route::get('/create-passenger', 'MainController@pageCreatePassenger');
    Route::get('/update-passenger/{id}', 'MainController@pageUpdatePassenger');
    Route::get('/driver/marketing/{driverId}', 'MainController@changeField');
    Route::get('/driver/send-email-libelula/{driverId}', 'MainController@sendEmailLibelula')
    ;
    Route::get('/driver-detail/{driverId}', 'MainController@driverDetail');
    Route::get('/approve-driver/{driverId}', 'MainController@approveDriver');
    Route::get('/reject-driver/{driverId}', 'MainController@rejectDriver');
    Route::get('/get-cities', 'MainController@getCities');
    Route::get('/get-roles', 'MainController@getRoles');
    Route::post('/image-save', 'MainController@imageSave');
    Route::get('/organization-by-city/{id}', 'MainController@organizationByCity');
    Route::get('/cities-by-region/{id}', 'MainController@citiesByRegion');
    Route::get('/driver/assing-me-driver/{id}', 'MainController@assingMeDriver');
});



Route::post('one-samsung-ajax', array('as' => 'ProcessController', 'uses' => 'ProcessController@ajaxOneSamsung'));
Route::get('/xml.xml', 'MainController@findXml');

Route::get('test-email/{number_sale}', array('as' => 'MainController', 'uses' => 'MainController@testMail'));
Route::get('categoria/{id}', array('as' => 'MainController', 'uses' => 'MainController@findCategory'));
Route::get('todos', array('as' => 'MainController', 'uses' => 'MainController@findProducts'));
Route::get('general', array('as' => 'MainController', 'uses' => 'MainController@findProductsWithOutPrice'));
Route::get('marca/{id}', array('as' => 'MainController', 'uses' => 'MainController@findProductByBrand'));
Route::get('grupo/{id}', array('as' => 'MainController', 'uses' => 'MainController@findProductByGroup'));
Route::get('producto/{slug}', array('as' => 'MainController', 'uses' => 'MainController@findProduct'));
Route::get('informacion/{slug}', array('as' => 'MainController', 'uses' => 'MainController@findInfo'));
Route::get('favoritos', array('as' => 'MainController', 'uses' => 'MainController@findWishlist'));
Route::get('test-queries', array('as' => 'MainController', 'uses' => 'MainController@findTestQueries'));

Route::get('buscar', array('as' => 'MainController', 'uses' => 'MainController@findSearchProduct'));

Route::get('ofertas', array('as' => 'MainController', 'uses' => 'MainController@findProductOffers'));

Route::get('terms-conditions', array('as' => 'MainController', 'uses' => 'MainController@findTerms'));

Route::get('/account', function(){
    return Redirect::to('account/my-account/3453421212');
});

Route::get('/tienda', function(){
    return Redirect::to('jugueteria');
});

Route::get('/inicio', function(){
    return Redirect::to('admin');
});

Route::get('{slug}/{extra_slug?}', array('as' => 'MainController', 'uses' => 'MainController@showPage'));


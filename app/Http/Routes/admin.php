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

Route::group(['prefix'=>'admin'], function(){
    Route::get('/', 'CustomAdminController@getRedirect');
    Route::get('redirect', 'CustomAdminController@getRedirect');
    
    Route::get('registro', 'CustomAdminController@getCustomerRegister');
    Route::post('registro', 'CustomAdminController@postCustomerRegister');
    Route::get('my-accounts', 'CustomAdminController@getMyAccounts');
    Route::post('edit-password', 'CustomAdminController@postEditPassword');
    Route::get('create-customer-dependant', 'CustomAdminController@getCustomerDependant');
    Route::post('create-customer-dependant', 'CustomAdminController@postCustomerDependant');
    Route::get('school-registrations', 'CustomAdminController@getSchoolRegistrationList');
    Route::get('create-school-registration/{customer_id}/{customer_dependant_id}', 'CustomAdminController@getSchoolRegistration');
    Route::post('create-school-registration', 'CustomAdminController@postSchoolRegistration');
    Route::get('my-payments', 'CustomAdminController@getMyPayments');
    Route::get('my-history', 'CustomAdminController@getMyHistory');
    Route::get('manual-pay/{id}', 'CustomAdminController@getManualPayment');

    Route::get('reportes-totales/{view?}', 'CustomAdminController@reportTotalesDrivers');
    Route::get('reportes-organization-total/{view?}', 'CustomAdminController@reportOrganizationTotalRequest');
    Route::get('reportes-users-amount/{view?}', 'CustomAdminController@reportUserAmount');

});



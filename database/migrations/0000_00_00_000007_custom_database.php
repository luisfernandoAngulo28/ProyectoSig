<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CustomDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('informations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });
        Schema::create('information_translation', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('information_id')->unsigned();
            $table->string('locale')->index();
            $table->string('slug')->nullable();
            $table->string('name')->nullable();
            $table->text('content')->nullable();
            $table->unique(['information_id','locale']);
            $table->foreign('information_id')->references('id')->on('informations')->onDelete('cascade');
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->boolean('gift')->default(0);
            $table->string('gift_name')->nullable();
            $table->string('gift_cellphone')->nullable();
            $table->string('gift_message')->nullable();
        });
        // Custom
        Schema::create('social_networks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned()->default(1);
            $table->integer('order')->nullable()->default(0);
            $table->string('code');
            $table->string('url');
            $table->timestamps();
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
        });
        Schema::create('titles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable();
            $table->timestamps();
        });
        Schema::create('title_translation', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('title_id')->unsigned();
            $table->string('locale')->index();
            $table->string('name');
            $table->unique(['title_id','locale']);
            $table->foreign('title_id')->references('id')->on('titles')->onDelete('cascade');
        });
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->nullable();
            $table->timestamps();
        });
        Schema::create('content_translation', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('content_id')->unsigned();
            $table->string('locale')->index();
            $table->text('content');
            $table->unique(['content_id','locale']);
            $table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');
        });
        Schema::create('contact_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });        

        // App Taxis
        Schema::create('organizations', function(Blueprint $table){
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->string('name')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('sindicato_id')->nullable();
            $table->enum('type', ['corporate-company', 'company'])->default('company');
            $table->boolean('active')->default(1);
            $table->string('commercial_name')->nullable();
            $table->integer('sprec_number')->nullable();
            $table->string('sprec_file')->nullable();
            $table->string('address')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('map')->nullable();
            $table->string('email')->nullable();
            $table->string('cellphone_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('logo_image')->nullable();
            $table->string('link_facebook')->nullable();
            $table->string('link_instagram')->nullable();
            $table->string('link_web_page')->nullable();
            $table->timestamps();
        });

        Schema::create('organization_phones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable();
            $table->integer('organization_id')->unsigned()->nullable();
            $table->integer('sindicato_id')->unsigned()->nullable();
            $table->date('date_birth')->nullable();
            $table->string('image')->nullable();
            $table->string('map')->nullable();
            $table->string('ci_number')->nullable();
            $table->boolean('is_verify')->default(0);
            $table->enum('type', ['representative', 'customer'])->default('customer');
            $table->string('client_socket_code')->nullable();
            $table->string('token_firebase')->nullable();
            $table->string('socket_id')->nullable();
            $table->text('token_jwt')->nullable();
            $table->boolean('is_connect')->default(0);
            $table->decimal('total_ratings')->nullable()->default(0);
            $table->integer('session_id')->nullable();
            $table->enum('gender', ['male','female'])->nullable()->default('male');

        });
        
        Schema::create('sindicatos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('city_id')->nullable();
            $table->timestamps();
        });

        Schema::create('organization_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });
        Schema::create('user_ratings', function (Blueprint $table ){
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('parent_id')->unsigned()->nullable(); // user_id ----> parent_id
            $table->integer('driver_id')->unsigned()->nullable();
            $table->integer('rating')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
        
        Schema::create('user_contacts', function (Blueprint $table ){
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable(); // user_id ----> parent_id
            $table->integer('user_id')->unsigned()->nullable();
            $table->enum('status', ['PENDING', 'ACCEPTED', 'REJECTED', 'REMOVED'])->nullable();
            $table->timestamps();
        });

        Schema::create('otps', function (Blueprint $table ){
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('parent_id')->unsigned();   // user_id ----> parent_id
            $table->string('code')->nullable();
            $table->bigInteger('time_expiration_code')->nullable();
            $table->timestamps();
        }); 
        
        Schema::create('drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('user_belongs_to_id')->nullable();
            $table->string('email')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('cellphone')->nullable();
            $table->integer('organization_id')->nullable();
            $table->integer('movil_number')->nullable();
            $table->string('image')->nullable();
            $table->string('municipal_registry_operator')->nullable();
            $table->string('municipal_registry_operator_file')->nullable();
            $table->string('license_front_image')->nullable();
            $table->string('license_back_image')->nullable();
            $table->string('ci_front_image')->nullable();
            $table->string('ci_back_image')->nullable();
            $table->string('tic')->nullable();
            $table->string('tic_file')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_expiration_date')->nullable();
            $table->boolean('active')->nullable()->default(1);
            $table->boolean('active_trips')->nullable()->default(0);
            $table->integer('number_of_passengers')->nullable()->nullable();
            $table->boolean('car_with_grill')->nullable()->default(0);
            $table->boolean('baby_chair')->nullable()->default(0);
            $table->boolean('travel_with_pets')->nullable()->default(0);
            $table->boolean('active_delivery')->nullable()->default(0);
            $table->boolean('fragile_content')->nullable()->default(0);
            $table->boolean('active_send_money')->nullable()->default(0);
            $table->decimal('amount_send_money', 10, 2)->nullable();
            $table->boolean('active_agent')->nullable()->default(0);
            $table->string('qr_image')->nullable();
            $table->string('device_code')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->boolean('is_active_for_career')->nullable()->default(0);
            $table->string('appkey')->nullable();
            $table->boolean('car_with_ac')->nullable()->default(0);
            $table->boolean('car_electric')->nullable()->default(0);
            $table->enum('gender', ['male','female'])->nullable()->default('male');
            $table->string('bank_account_number')->nullable();
            $table->integer('bank_id')->nullable();
            $table->integer('ci_number')->nullable();
            $table->enum('ci_exp', ['LP','CH', 'CB', 'BE', 'OR', 'PA', 'PO', 'SC', 'TA'])->nullable();
            
            $table->boolean('marketing_check')->nullable()->default(0);
            $table->boolean('legal_check')->nullable()->default(0);
            $table->boolean('send_email_libelula')->nullable()->default(0);

            
            $table->string('name_titular')->nullable()->default(null);
            $table->string('ci_number_titular')->nullable()->default(null);
            $table->string('ci_front_image_titular')->nullable()->default(null);
            $table->string('ci_back_image_titular')->nullable()->default(null);
            
            $table->timestamps();
        });
        Schema::create('vehicle_brands', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->string('name')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('vehicle_brand_id')->unsigned();
            $table->string('name')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
        Schema::create('driver_vehicles', function (Blueprint $table ){
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('parent_id')->unsigned();    // driver_id -----> parent_id
            $table->integer('city_id')->unsigned();
            $table->string('number_plate')->nullable();
            $table->string('vehicle_image')->nullable();
            $table->string('side_image')->nullable();
            $table->string('color')->nullable();
            $table->string('model_year')->nullable();
            $table->enum('type', ['vagoneta', 'multiuso', 'convertible', 'descapotable'])->nullable();
            $table->string('tmov')->default('')->nullable();
            $table->string('chassis_number')->default('')->nullable();
            $table->integer('rua')->default(0)->nullable();
            $table->string('rua_image')->default(0)->nullable();
            $table->string('municipal_registry_vehicle')->nullable();
            $table->integer('vehicle_brand_id')->nullable();
            $table->integer('vehicle_model_id')->nullable();
            $table->boolean('active')->default(1);
            $table->string('vehicle_engine')->nullable();
            $table->timestamps();
        });
        Schema::create('driver_ratings', function (Blueprint $table ){
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('parent_id')->unsigned();   // driver_id -----> parent_id
            $table->integer('user_id')->unsigned();
            $table->integer('driver_rating')->nullable();
            $table->integer('vehicle_rating')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        }); 
        Schema::create('driver_activations', function(Blueprint $table){
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('parent_id')->unsigned();    // driver_id -----> parent_id
            $table->date('initial_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('initial_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('status', ['active', 'busy'])->nullable();
            $table->timestamps();
        });
        Schema::create('requests', function(Blueprint $table){
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->enum('status', ['ACCEPTED', 'REQUIRED', 'COMPLETED', 'CANCELLED'])->nullable();
            $table->integer('type_request_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('payment_method_id')->unsigned();
            $table->decimal('offered_price', 10, 2)->nullable();
            $table->enum('type', ['trip', 'delivery'])->nullable();
            $table->string('distance')->nullable();
            $table->integer('requested_rating')->nullable();
            $table->string('taxi_company')->nullable();
            $table->string('comments')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->boolean('is_counterofferable')->default(0);
            $table->boolean('is_scheduling')->default(0);
            $table->string('date')->nullable();
            $table->string('hour')->nullable();
            $table->timestamps();
        });
        Schema::create('request_waypoints', function(Blueprint $table){
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('parent_id')->unsigned();   // request_id -----> parent_id
            $table->enum('type', ['driver', 'origin', 'checkpoint', 'destination'])->nullable();
            $table->string('address')->nullable();
            $table->integer('order')->nullable();
            $table->enum('status', ['holding', 'scheduled', 'pick-up', 'delivered'])->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();
        });
        Schema::create('type_requests', function(Blueprint $table){
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('logo_image')->nullable();
            $table->string('text_color')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
        Schema::create('request_trips', function(Blueprint $table){
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('parent_id')->unsigned();       // request_id -----> parent_id
            $table->integer('number_of_passengers')->default(0);
            $table->boolean('car_with_grill')->default(0);
            $table->boolean('baby_chair')->default(0);
            $table->boolean('travel_with_pets')->default(0);
            $table->timestamps();
        });

        Schema::create('driver_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('driver_id')->unsigned();
            $table->integer('request_id')->unsigned();
            $table->decimal('counteroffer', 10, 2)->nullable();
            $table->enum('status', ['accepted', 'rejected', 'counteroffered', 'pending', 'timeout'])->nullable();
            $table->enum('user_status', ['accepted', 'rejected', 'none'])->nullable();
            $table->boolean('is_available_request')->default(0);
            $table->string('user_to_notify')->nullable();
            $table->timestamps();
        });

        Schema::create('rides', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('parent_id')->unsigned();
            $table->integer('driver_id')->unsigned();
            $table->integer('user_id')->unsigned()->default(0);
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('status', ['START_TRIP', 'DRIVER_ARRIVED', 'START_SERVICE', 'END_TRIP', 'TRIP_CANCELLED'])->nullable();
            $table->string('chat_id')->nullable();
            $table->integer('product_bridge_id')->nullable();
            $table->integer('sale_id')->nullable();
            $table->timestamp('start_timestamp')->nullable();
            $table->timestamps();
        });

        Schema::create('ride_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned(); // ride_id -----> parent_id
            $table->integer('user_id')->unsigned();
            $table->string('reason')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id')->nullable(); // 
            $table->decimal('base_rate')->nullable();
            $table->decimal('km_rate')->nullable();
            $table->decimal('time_rate')->nullable();
            $table->timestamps();
        });

        Schema::create('driver_payment_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable(); // driver_id -----> parent_id
            $table->integer('payment_method_id')->nullable();
            $table->boolean('active')->default(0);
            $table->timestamps();
        });
        
        Schema::create('support_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->string('question')->nullable();
            $table->string('answer')->nullable();
            $table->boolean('active')->default(0);
            $table->timestamps();
        });
        Schema::create('request_bring_money', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->nullable();
            $table->integer('cash_amount_required')->nullable();
            $table->timestamps();
        });

        Schema::create('driver_device_code', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();    // driver_id es el padre
            $table->enum('status', ['active', 'desactive'])->nullable();
            $table->string('device_code')->nullable();
            $table->timestamps();
        });

        Schema::create('panic_button', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();    // user_id es el padre
            $table->enum('status', ['active'])->nullable()->default('active');
            $table->string('time')->nullable();
            $table->timestamps();
        });
        Schema::create('fixed_price_cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id')->nullable();    // city_id es el padre
            $table->integer('price')->nullable();    // user_id es el padre
            $table->timestamps();
        });
        Schema::create('firebase_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['city', 'organization', 'birthday'])->nullable();
            $table->string('content')->nullable();
            $table->timestamps();
        });
        
        Schema::create('driver_configuration_distance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable();    // driver_id es el padre
            $table->enum('type', ['distance', 'time'])->nullable();
            $table->string('total')->nullable();
            $table->timestamps();
        });

        Schema::create('banks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(); // 
            $table->string('abbreviation')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   
        Schema::dropIfExists('banks');
        Schema::dropIfExists('driver_configuration_distance');
        Schema::dropIfExists('firebase_notifications');
        Schema::dropIfExists('fixed_price_cities');
        Schema::dropIfExists('panic_button');
        Schema::dropIfExists('driver_device_code');
        Schema::dropIfExists('request_bring_money');
        Schema::dropIfExists('support_questions');
        Schema::dropIfExists('driver_payment_methods');
        Schema::dropIfExists('rates');
        Schema::dropIfExists('ride_reports');
        Schema::dropIfExists('rides');
        Schema::dropIfExists('driver_requests');
        Schema::dropIfExists('request_trips');
        Schema::dropIfExists('type_requests');
        Schema::dropIfExists('request_waypoints');
        Schema::dropIfExists('requests');
        Schema::dropIfExists('driver_activations');
        Schema::dropIfExists('driver_ratings');
        Schema::dropIfExists('driver_vehicles');
        Schema::dropIfExists('vehicle_models');
        Schema::dropIfExists('vehicle_brands');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('otps');
        Schema::dropIfExists('user_contacts');
        Schema::dropIfExists('user_ratings');
        Schema::dropIfExists('sindicatos');
        Schema::dropIfExists('organization_users');
        Schema::dropIfExists('organization_phones');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('information_translation');
        Schema::dropIfExists('informations');
        Schema::dropIfExists('contact_forms');
        Schema::dropIfExists('content_translation');
        Schema::dropIfExists('contents');
        Schema::dropIfExists('title_translation');
        Schema::dropIfExists('titles');
        Schema::dropIfExists('social_networks');

    }
}

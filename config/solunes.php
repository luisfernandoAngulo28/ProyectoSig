<?php

return [
	// GLOBAL
	'vendor_path' => env('SOLUNES_PATH', 'vendor/solunes/master'),
	'blocked_activities' => [],
	'master_admin_id' => 1,
	'master_dashboard' => false,
	'socialite' => true,
	'inbox' => true,
	'google_maps_key'=> "AIzaSyCMoWbY7kEIpWBAfegxCrNJFAzW7gFiNVM", //

	'customer_dashboard' => true,
	'customer_dashboard_nodes' => [
		'user' 			    => ['delete'=>true],
		'organization'      => ['create' => true, 'delete' => true],
		'city'      => ['create' => true, 'delete' => true, 'edit' => true, 'view' => true],
		'driver' 		    => [],
		'driver-vehicle'    => [],
		'driver-rating'     => [],
		'user-rating'     	=> [],
		'driver-activation' => [],
		'vehicle-brand' 	=> [],
		'vehicle-model' 	=> [],
		'request' 		    => [],
		'request-waypoint'  => [],
		'request-trip' 		=> [],
		// 'sindicato' 		=> [],
		'panic-button' 		=> [],
		'firebase-notification' => [],
		'driver-device-code' => ['delete' => true],
	],

	'customer_dashboard_filters' => false,
	'customer_dashboard_custom_filters' => true,

	'app_name' => 'TAXIS APP',
	'content_images_table' => true, // Crear tabla de imagenes para contenido variado

	// MAP SERVICE
	'default_location' => '-17.783519;-63.182194',

	// PLUGINS
	'business' => true,
	'payments' => true,
	'sales' => true,
	'customer' => true,
	'product' => false,
	'inventory' => false,
	'notification' => true,

	// GLOBAL
	'login_instructions' => false,
	'admin_initial_menu' => [
		'login' => true,
		'password_recover' => true,
		'dashboard' => false,
		'my_account' => false,
		'my_profile' => false,
		'logout' => true
	],
	'admin_inbox_disabled' => true,
	'admin_inbox_excluded' => ['member'], // Incluir roles a ser excluidos del inbox, por defecto member

	// SOCIALITE
	'socialite_google' => false,
	'socialite_facebook' => false,
	'socialite_twitter' => false,
	'socialite_github' => false,

	// CUSTOM FUNC
	'get_page_array' => false,
	'before_migrate' => false,
	'after_migrate' => false,
	'before_seed' => false,
	'after_seed' => true,
	'after_login' => true,
	'custom_get_items' => true,
	'get_sitemap_array' => false,
	'get_indicator_result' => false,
	'update_indicator_values' => false,
	'check_permission' => true,
	'custom_indicator' => false,
	'custom_field' => false,
	'get_options_relation' => true,
	'check_custom_filter' => false,
	'custom_filter' => false,
	'custom_filter_field' => false,
	'custom_pdf_header' => false,
	'custom_admin_node_actions' => true,
	'custom_admin_field_actions' => true,
	'custom_admin_get_list' => true,
	'custom_admin_get_item' => true,
	// 'subadmin_table_pagination_count' => 10,

	// LIST
	'pagination_count' => 500,
	'subadmin_pagination_count' => 1000,
	'list_horizontal_scroll' => false,
	'list_vertical_scroll' => 0, // En pixeles
	'table_pagination' => 'false',
	'table_pagination_count' => 25,
	'subadmin_table_pagination' => 'true',
	'subadmin_table_pagination_count' => 1000,
	'list_inline_edit' => false,
	'list_export_pdf' => true,
	'filter_suboptions' => false,
	'filter_suboptions_exceptions' => [],
	'delete_item_custom_message' => false,

	// CUSTOM HOOKS
	'item_form_add_html_after_form' => true,
	'item_form_add_html_before_button' => true,

];

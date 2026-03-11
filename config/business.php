<?php

return [

	// GENERAL
	'after_seed' => true,
	'seed_currencies' => true,
	'seed_regions' => true,
	'seed_agencies' => true,
	'product_images' => true,
	'product_variations' => true,
	'main_exchange' => 6.96,
	'pricing_rules' => true,
    'brands' => true,
	'product_barcode' => true,
	'online_store_agency_id' => 1,

	// INTEGRATIONS
	'gitlab' => false,
	'gitlab_api_key' => env('GITLAB_API_KEY'),
	'hubspot' => false,
	'hubspot_api_key' => env('HUBSPOT_API_KEY'),

	// CUSTOM FORMS
    'item_get_after_vars' => ['purchase','product'], // array de nodos: 'node'
    'item_child_after_vars' => ['product'],
    'item_remove_scripts' => ['purchase'=>['leave-form']],
    'item_add_script' => ['purchase'=>['barcode-product'], 'product'=>['product']],

];
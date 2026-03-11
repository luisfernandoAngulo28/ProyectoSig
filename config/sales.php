<?php

return [

	// GENERAL
	'after_seed' => true,
	'desk_sale' => false,
	'delivery' => true,
	'delivery_country' => false,
	'delivery_city' => true,
	'ask_address' => true,
	'ask_coordinates' => true,
	'seed_shipping' => true,
	'sales_email' => true,
	'sales_cellphone' => true,
	'sales_username' => false,
	'ask_invoice' => true,
	'check_cart_stock' => true,
	'credit' => false,
	'refunds' => false,
	'company_relation' => false,
	'contact_relation' => false,
	'sales_agency' => true,

	'generate_invoice_pagostt'=> false, // para que no se emita factura

	// INTEGRATIONS
	'solunes_project' => false,

	// DELIVERY
	'delivery' => true,
	'ask_address' => true,
	'seed_shipping' => true,
	
	// ACTIVE SHIPPING METHODS
	'own-office' => false,
	'unibol' => false,
	'ocs' => true,
	'dhl' => false,
	
	// CUSTOM FORMS
    'item_get_after_vars' => ['purchase','product'], // array de nodos: 'node'
    'item_child_after_vars' => ['product'],
    'item_remove_scripts' => ['purchase'=>['leave-form']],
    'item_add_script' => ['purchase'=>['barcode-product'], 'product'=>['product']],

];
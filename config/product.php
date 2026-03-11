<?php

return [

	// GENERAL
	'after_seed' => true,
	'product_images' => true,
	'product_packages' => false,
	'product_benefits' => false,
	'product_groups' => false,
	'product_offers' => true,
	'product_variations' => true,
	'product_extras' => false,
	'product_url' => true,

	// CUSTOM FORMS
    'item_get_after_vars' => ['purchase','product'], // array de nodos: 'node'
    'item_child_after_vars' => ['product'],
    'item_remove_scripts' => ['purchase'=>['leave-form']],
    'item_add_script' => ['purchase'=>['barcode-product'], 'product'=>['product']],

];
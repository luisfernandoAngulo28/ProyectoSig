<?php

return [

	'redirect_after_payment' => 'account/my-payments/1913489491',
	'sfv_version' => 1,
	'discounts' => true,
	'customer_cancel_payments' => true,

	// ACTIVE PAYMENT METHODS
	'manual' => true,
	'cash' => false,
	'bank-deposit' => false,
	'pagostt' => true,
	'paypal' => false,
	'payme' => false,
	'tigo-money' => false,
	'pagosnet' => false,

		/* Para libelula */
    'todotix_params' => [
		'testing' => env('ENABLE_TESTING', 0), // Utilizar el ambiente de pruebas
		'main_server' => 'http://www.todotix.com:10365/rest/', // URL DE PagosTT para Producción
		'test_server' => 'http://www.todotix.com:10365/rest/', // URL DE PagosTT para Pruebas
		'salt' => 'GfFJo519zBd7gzmIBhNd0vBK2Co375bS', // Llave de encriptación, reemplazar por la del proyecto
		'secret_iv' => '!IV@_$2', // Secret IV de encriptación, reemplazar por oficial de cuentas 365
		'secret_pagostt_iv' => '!#$a54?3', // Secret IV de encriptación de PAgosTT, integrado con Beto
		'app_name' => env('APP_NAME', 'PagosTT'), // Nombre enviado a Cuentas365
		'app_key' => 'ec1ace28-67d7-4e0c-9e3e-9e7ca3b8b636', // AppKey generado por PagosTT
		'test_app_key' => 'ec1ace28-67d7-4e0c-9e3e-9e7ca3b8b636', // AppKey generado por PagosTT
		'custom_app_keys' => ['default'=>'7273976a-6353-2c88-6f19-cfda845cf6b9','test'=>'ec1ace28-67d7-4e0c-9e3e-9e7ca3b8b636'], // AppKey personalizados para ser utilizados
		'invoice_server' => 'http://www.todotix.com:7777/factura/', // Servidor donde se almacenan las facturas, pegado al Invoice ID
		'invoice_test_server' => 'http://todotix.com:7777/factura/', // Servidor donde se almacenan las facturas en pruebas, pegado al Invoice ID
		'notify_email' => true, // Notificar la recepción del pago por correo electrónico
		'enable_cashier' => true, // Definir si se habilita el pago en caja
		'cashier_payments' => ['default'=>'4aa98127e5611fedd2350ae3211a40612973876c3b09315330c92a22f16873df'], // Definir la llave de pagos en caja de PagosTT para la compañia
		'test_cashier_payments' => ['default'=>'ff65dd3fc4e1ac37bd6bfc61dca2cf8b3c14c37416d61dd97a316dd5ee900bb6'], // Definir la llave de pagos en caja de PagosTT para la compañia en Modo Testing
		'enable_bridge' => false, // Habilitar si no se utilizarán los módulos de pagos de Solunes
		'enable_cycle' => false, // Habilitar la facturación por ciclos
		'enable_preinvoice' => false, // Habilitar la generación de prefacturas
		'finish_payment_verification' => false, // Habilitar si se desea realizar la verificación final
		'customer_all_payments' => true, // Habilitar si se desea aceptar pagos en masa
		'customer_recurrent_payments' => false, // Habilitar si se desea integrar a Cuentas365
		'is_cuentas365' => false, // Habilitar la plataforma es Cuentas365
	],
		/* Para qhantuy */
	'pagostt_params' => [
		'testing' => env('ENABLE_TESTING', 0), // Utilizar el ambiente de pruebas
		'main_server' => 'https://qpos-prod.qhantuy.com/external-api/', // URL DE PagosTT para Producción
		'test_server' => 'https://qpos-dev.qhantuy.com/external-api/', // URL DE PagosTT para Pruebas
		'salt' => 'GfFJo519zBd7gzmIBhNd0vBK2Co375bS', // Llave de encriptación, reemplazar por la del proyecto
		'secret_iv' => '!IV@_$2', // Secret IV de encriptación, reemplazar por oficial de cuentas 365
		'secret_pagostt_iv' => '!#$a54?3', // Secret IV de encriptación de PAgosTT, integrado con Beto
		'app_name' => env('APP_NAME', 'PagosTT'), // Nombre enviado a Cuentas365
		'app_key' => 'Vm0xMFlXRXlVWGhTYmxKWFltdHdUMVpzVm5kVmJGcHlWV3RLVUZWVU1Eaz0=-XNGCT', // AppKey generado por PagosTT
		'test_app_key' => 'Vm0xMFlXRXlVWGxVYmxKV1lXczFVbFpyVWtKUFVUMDk=-XFRGG', // AppKey generado por PagosTT
		'custom_app_keys' => ['default'=>'Vm0xMFlXRXlVWGhTYmxKWFltdHdUMVpzVm5kVmJGcHlWV3RLVUZWVU1Eaz0=-XNGCT'], // AppKey personalizados para ser utilizados
		'custom_test_app_keys' => ['default'=>'Vm0xMFlXRXlVWGxVYmxKV1lXczFVbFpyVWtKUFVUMDk=-XFRGG'], // AppKey personalizados para ser utilizados en modo Testing
		'invoice_server' => 'http://www.todotix.com:7777/factura/', // Servidor donde se almacenan las facturas, pegado al Invoice ID
		'invoice_test_server' => 'http://todotix.com:7777/factura/', // Servidor donde se almacenan las facturas en pruebas, pegado al Invoice ID
		'notify_email' => true, // Notificar la recepción del pago por correo electrónico
		'enable_cashier' => false, // Definir si se habilita el pago en caja
		'cashier_payments' => ['default'=>'ffd4b449600a5b1059702ae7578838f824dcb754ec8fc40f70ecb64c500e7fd4'], // Definir la llave de pagos en caja de PagosTT para la compañia
		'test_cashier_payments' => ['default'=>'ffd4b449600a5b1059702ae7578838f824dcb754ec8fc40f70ecb64c500e7fd4'], // Definir la llave de pagos en caja de PagosTT para la compañia en Modo Testing
		'enable_bridge' => false, // Habilitar si no se utilizarán los módulos de pagos de Solunes
		'enable_cycle' => false, // Habilitar la facturación por ciclos
		'enable_preinvoice' => false, // Habilitar la generación de prefacturas
		'finish_payment_verification' => false, // Habilitar si se desea realizar la verificación final
		'customer_all_payments' => true, // Habilitar si se desea aceptar pagos en masa
		'customer_recurrent_payments' => false, // Habilitar si se desea integrar a Cuentas365
		'is_cuentas365' => false, // Habilitar la plataforma es Cuentas365
	],

	// PARAMETROS
	'scheduled_transactions' => false,
	'invoices' => true,
	'shipping' => true,
	'online_banks' => true,

	// SEGURIDAD Y ENCRIPTACION
	'salt' => 'GfFJo519zBd7gzmIBhNd0vBK2Co375bS', // Llave de encriptación, reemplazar por la del proyecto
	'secret_iv' => '!IV@_$2', // Secret IV de encriptación, reemplazar por oficial de cuentas 365
	
	// PAGOSTT
	'pagostt_app_key' => 'c26d8c99-8836-4cd5-a850-230c9d3fbf3c', // AppKey generado por PagosTT
	'paypal_app_key' => 'c26d8c99-8836-4cd5-a850-230c9d3fbf3c', // AppKey generado por PagosTT

];
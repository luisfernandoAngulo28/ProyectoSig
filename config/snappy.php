<?php

if(env('APP_SYSTEM', 'windows')=='windows'){
    $binary_folder = '';
    $zoom = '0.75';
} else {
    $binary_folder = '/usr/local/bin/';
    $zoom = '0.6';
}
if(env('PDF_ZOOM')){
    $zoom = env('PDF_ZOOM');
}

return array(


    'pdf' => array(
        'enabled' => true,
        'binary' => $binary_folder.'wkhtmltopdf',
        'timeout' => false,
        'options' => array('dpi'=>'120', 'viewport-size'=>'1280x1024', 'margin-top'=>'0', 'margin-bottom'=>'0', 'margin-left'=>'0', 'margin-right'=>'0', 'disable-smart-shrinking'=>true, 'zoom'=>$zoom,'load-error-handling' => 'ignore', 'load-media-error-handling' => 'ignore', 'enable-local-file-access'=>true),
        'env' => ['load-error-handling', 'load-media-error-handling']        
    ),
    'image' => array(
        'enabled' => true,
        'binary' => $binary_folder.'wkhtmltoimage',
        'timeout' => false,
        'options' => array(),
    ),


);

<?php

Route::get('artisan/send-report', function () {
  Artisan::call('send-report');
  return dd(Artisan::output());
});
/*Route::get('/updateapp', function()
{
    \Artisan::call('config:cache');
    //echo 'dump-autoload complete';
});*/
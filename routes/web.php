<?php

use Illuminate\Support\Facades\Route;


//Route::get('/', function () {
//    return view('home');
//});

Route::post('/test-post', function () {
    return 'POST request reaching web.php';
});

require __DIR__.'/auth.php';

require __DIR__.'/pdf.php';
require __DIR__ . '/two_fa.php';
require __DIR__ . '/token.php';

Route::view('/{any?}', 'layouts.app')->where('any', '.*');

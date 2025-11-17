<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return redirect('/events');
});

require __DIR__.'/test.php';

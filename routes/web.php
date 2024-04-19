<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParsingController;

Route::get('/', function () {
    return view('index');
});

Route::post('/parse', [ParsingController::class, 'parse']);

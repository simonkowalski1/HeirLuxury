<?php

use Illuminate\Support\Facades\Route;

// You don't have to use this yet; it just needs to exist.
Route::get('/ping', function () {
    return ['status' => 'ok'];
});

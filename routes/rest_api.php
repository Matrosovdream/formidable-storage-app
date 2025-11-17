<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'rest'], function () {

    // v1 routes
    Route::group(['prefix' => 'v1'], function () {
        
        // Status endpoint
        Route::get('/status', function (Request $request) {
            return response()->json([
                'status' => 'OK',
                'version' => '1.0.0',
                'timestamp' => now()->toIso8601String(),
            ]);
        });

    });

});
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Rest\v1\FrmEntryHistoryController;
use App\Http\Controllers\Rest\v1\FrmFieldsController;

Route::prefix('rest')
    ->middleware('rest.token')
    ->group(function () {

        Route::prefix('v1')->group(function () {

            // Entry routes
            Route::prefix('entry')->group(function () {

                Route::prefix('history')->group(function () {

                    Route::post('/update', [FrmEntryHistoryController::class, 'update']);
                    //Route::post('/list', [FrmEntryHistoryController::class, 'list']);
                    Route::post('/view/{id}', [FrmEntryHistoryController::class, 'getEntryHistory']);

                });

            });

            // Frm fields routes
            Route::prefix('fields')->group(function() {

                Route::post('/update-all', [FrmFieldsController::class, 'updateAll']);

            });

            Route::get('/status', function () {
                return response()->json([
                    'status' => 'OK',
                    'version' => '1.0.0',
                    'timestamp' => now()->toIso8601String(),
                ]);
            }); 

        });

    });

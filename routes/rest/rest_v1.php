<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrmEntryHistoryController;

Route::prefix('rest')
    ->middleware('rest.token')
    ->group(function () {

        Route::prefix('v1')->group(function () {

            Route::prefix('entry')->group(function () {

                Route::prefix('history')->group(function () {

                    Route::post('/update', [FrmEntryHistoryController::class, 'update']);

                    //Route::get('/list', [FrmEntryHistoryController::class, 'list']);
                    //Route::get('/view/{id}', [FrmEntryHistoryController::class, 'view']);

                });

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

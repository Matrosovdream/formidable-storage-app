<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\DataController;

require __DIR__ . '/api.php';



// API routes 
Route::group(['prefix' => 'api'], function() {

    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Authentication routes
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/register', [RegisteredUserController::class, 'store']);   
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

    // Site routes
    Route::group(['prefix' => 'sites'], function() {
        
        Route::get('/list', [SiteController::class, 'list'])->name('api-sites-list');
        Route::get('/view/{site_id}', [SiteController::class, 'view'])->name('api-sites-view');
        Route::get('/create', [SiteController::class, 'create'])->name('api-sites-create');
        Route::post('/store', [SiteController::class, 'store'])->name('api-sites-store');
        Route::delete('/delete/{site_id}', [SiteController::class, 'delete'])->name('api-sites-delete');

    })->middleware('auth:sanctum');

    // Data routes
    Route::group(['prefix' => 'data', 'middleware' => 'auth:sanctum'], function() {

        Route::get('/entries/{site_id}', [DataController::class, 'entries'])->name('api-data-entries');
        Route::get('/entries/{site_id}/{entry_id}/updates', [DataController::class, 'entryUpdates'])->name('api-data-entry-updates');
        Route::get('/entries/{site_id}/{entry_id}/emails', [DataController::class, 'entryEmails'])->name('api-data-entry-emails');

    });

});



// Catch-all for SPA, but NOT for for the listed prefixes
Route::view('/{any}', 'spa')->where('any', '^(?!api|sanctum|rest|health).*$');

// Main login route
Route::get('/login', function () {

})->name('login');


Route::get('/health', function () {
    $status = [];

    // Check Database Connection
    try {
        DB::connection()->getPdo();
        // Optionally, run a simple query
        DB::select('SELECT 1');
        $status['database'] = 'OK';
    } catch (\Exception $e) {
        $status['database'] = 'Error';
    }

    // Check Redis Connection
    try {
        Cache::store('redis')->put('health_check', 'OK', 10);
        $value = Cache::store('redis')->get('health_check');
        if ($value === 'OK') {
            $status['redis'] = 'OK';
        } else {
            $status['redis'] = 'Error';
        }
    } catch (\Exception $e) {
        $status['redis'] = 'Error';
    }

    // Check Storage Access
    try {
        $testFile = 'health_check.txt';
        Storage::put($testFile, 'OK');
        $content = Storage::get($testFile);
        Storage::delete($testFile);

        if ($content === 'OK') {
            $status['storage'] = 'OK';
        } else {
            $status['storage'] = 'Error';
        }
    } catch (\Exception $e) {
        $status['storage'] = 'Error';
    }

    // Determine overall health status
    $isHealthy = collect($status)->every(function ($value) {
        return $value === 'OK';
    });

    $httpStatus = $isHealthy ? 200 : 503;

    return response()->json($status, $httpStatus);
});

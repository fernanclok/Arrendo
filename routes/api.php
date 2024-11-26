<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\PropertyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Contracts
Route::prefix('contracts')->group(function () {
    Route::get('/', [ContractController::class, 'index']);
    Route::post('/create', [ContractController::class, 'store']);
    Route::get('/user_tenant', [ContractController::class, 'getTenantUsers']);
});


// zones
Route::get('/zones', [ZoneController::class, 'getZones']);

//Comments
Route::prefix('comments')->group(function () {
    Route::get('/{propertyId}', [PropertyController::class, 'getComments']);
    Route::post('/', [PropertyController::class, 'createComment']);
});

// properties
Route::prefix('properties')->group(function () {
    Route::get('/', [PropertyController::class, 'get']);
    Route::post('/create', [PropertyController::class, 'create']);
    Route::get('/filter', [PropertyController::class, 'getFilteredProperties']);
    Route::get('/getProperties', [PropertyController::class, 'getProperties']);
    Route::get('/featuredProperties', [PropertyController::class, 'featuredProperties']);
});

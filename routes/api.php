<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\MaintenanceController;

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

// properties
Route::prefix('properties')->group(function () {
    Route::get('/', [PropertyController::class, 'get']);
    Route::post('/create', [PropertyController::class, 'create']);
    Route::get('/filter', [PropertyController::class, 'getFilteredProperties']);
    Route::get('/getProperties', [PropertyController::class, 'getProperties']);
    Route::get('/getPropertyDetails/{id}', [PropertyController::class, 'getPropertyDetails']);
});
//Maintenace
Route::prefix('maintenance')->group(function () {
    Route::get('/', [MaintenanceController::class, 'index']);
    Route::post('/store', [MaintenanceController::class, 'store']);
  
    Route::patch('/{id}',[MaintenanceController::class, 'update']);
});
//MaintenaceOwner
Route::prefix('maintenanceOwner')->group(function () {
    Route::get('/properties', [MaintenanceController::class, 'getProperties']); // Listar propiedades
    Route::get('/maintenancesReq', [MaintenanceController::class, 'getRequestsByProperty']); // Listar solicitudes por propiedad
    Route::put('/maintenancesReq/{id}', [MaintenanceController::class, 'updateRequest']);
  
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\PropertyController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\RentalApplicationController;
use App\Http\Controllers\AppointmentController;
use App\Models\Appoinment;
<<<<<<< HEAD
use App\Models\Rental_application;
=======
>>>>>>> 45ca4fc1e2666bf9abf20419e6974d4074045a43

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

// contracts
Route::prefix('contracts')->group(function () {
    Route::get('/', [ContractController::class, 'index']);
    Route::get('{id}', [ContractController::class, 'getContract']);
    Route::post('/create', [ContractController::class, 'store']);
    Route::get('/get/user_tenant', [ContractController::class, 'getTenantUsers']);
<<<<<<< HEAD
    Route::get('/tenant/{id}', [ContractController::class, 'getTenantContract']);
=======
>>>>>>> 45ca4fc1e2666bf9abf20419e6974d4074045a43
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

    Route::get('/getPropertyDetails/{id}', [PropertyController::class, 'getPropertyDetails']);
    Route::post('/appointment', [AppointmentController::class, 'createAppoinment']);
    Route::get('/applications', [PropertyController::class, 'getAllApplications']);
    Route::post('/applicate', [PropertyController::class, 'createApplication']);
<<<<<<< HEAD
    Route::post('/document-application', [RentalApplicationController::class, 'uploadDocument']);
    Route::get('/last', [RentalApplicationController::class, 'lastApplicationCreated']);
=======
>>>>>>> 45ca4fc1e2666bf9abf20419e6974d4074045a43
});

Route::get('/properties/{id}', [PropertyController::class, 'show']);
Route::put('/properties/{id}', [PropertyController::class, 'update']);
Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);

// appointments
Route::prefix('appointments')->group(function () {
    Route::get('/',[AppointmentController::class, 'getUserAppointments']);
<<<<<<< HEAD
    Route::get('/requests', [AppointmentController::class, 'getOwnerRequests']);
    Route::put('/update', [AppointmentController::class, 'updateAppointment']);
=======
>>>>>>> 45ca4fc1e2666bf9abf20419e6974d4074045a43
});

// dashboard
Route::get('/payment-history/{tenantUserId}', [DashboardController::class, 'getPaymentHistory']);
Route::get('/rented-property/{tenantUserId}', [DashboardController::class, 'getRentedProperty']);
<<<<<<< HEAD
Route::get('/notifications/{userId}', [DashboardController::class, 'getNotifications']);
Route::put('/notifications/{id}/read', [DashboardController::class, 'markAsRead']);
Route::put('/notifications/{id}/unread', [DashboardController::class, 'markAsUnread']);

// rental application
=======

>>>>>>> 45ca4fc1e2666bf9abf20419e6974d4074045a43
Route::prefix('rental-applications')->group(function(){
    Route::get('/', [RentalApplicationController::class, 'index']);
    Route::post('/{id}/approve', [RentalApplicationController::class, 'approve']);
    Route::post('/{id}/reject', [RentalApplicationController::class, 'reject']);
});

//Maintenace
Route::prefix('maintenance')->group(function () {
    Route::get('/', [MaintenanceController::class, 'index']);
<<<<<<< HEAD
    Route::get('/maintenancesReqOwner', [MaintenanceController::class, 'getRequestsByProperty']);
    Route::get('/getPropertyByTenant', [MaintenanceController::class, 'getPropertyByTenant']);
    Route::post('/store', [MaintenanceController::class, 'store']);
    Route::patch('/{id}',[MaintenanceController::class, 'update']);
    Route::put('/maintenancesReqOwner/{id}', [MaintenanceController::class, 'updateRequest']);
   
});
=======
    Route::post('/store', [MaintenanceController::class, 'store']);
    Route::patch('/{id}',[MaintenanceController::class, 'update']);
    Route::get('/getPropertyByTenant', [MaintenanceController::class, 'getPropertyByTenant']);
});
//MaintenaceOwner
Route::prefix('maintenanceOwner')->group(function () {
    Route::get('/properties', [MaintenanceController::class, 'getProperties']); // Listar propiedades
    Route::get('/maintenancesReq', [MaintenanceController::class, 'getRequestsByProperty']); // Listar solicitudes por propiedad
    Route::put('/maintenancesReq/{id}', [MaintenanceController::class, 'updateRequest']);
  
});


>>>>>>> 45ca4fc1e2666bf9abf20419e6974d4074045a43

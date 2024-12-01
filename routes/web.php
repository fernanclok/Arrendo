<?php

use App\Http\Controllers\ContractController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Broadcasting\BroadcastController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', []);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Dashboard
Route::middleware(['auth', 'verified', 'role:Owner'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/settings', [DashboardController::class, 'index'])->name('dashboard.settings');
});

//properties
Route::get('/properties', function () {
    return Inertia::render('Properties');
})->name('properties');

//my properties
Route::get('/my-properties', function () {
    return Inertia::render('MyProperties', [
        'user' => auth()->user()
    ]);
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('myProperties');

//search properties
Route::get('/search-properties', function () {
    return Inertia::render('SearchProperties');
})->middleware(['auth', 'verified', 'role:Tenant'])->name('searchProperties');

// contracts
Route::get('/contracts', function () {
    return Inertia::render('Contracts/showContract');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('contracts');

Route::get('/manage/contracts', function () {
    return Inertia::render('Contracts/manageContracts');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('manageContracts');

Route::get('/contracts-details/{id}', function ($id) {
    return Inertia::render('Contracts/detailsContract');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('ContractDetails');

Route::get('/all-contracts', function () {
    return Inertia::render('Contracts/allContract');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('AllContracts');

Route::get('/all-contracts/tenant', function () {
    return Inertia::render('Contracts/tenantContracts');
})->middleware(['auth', 'verified', 'role:Tenant'])->name('TenantContracts');

// rental applications
Route::get('/TrackRequest', function () {
    return Inertia::render('TrackRequest');
})->middleware(['auth', 'verified', 'role:Tenant'])->name('TrackRequest');

Route::get('/EvaluateRequest', function () {
    return Inertia::render('EvaluateRequest');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('EvaluateRequest');

//appointments
Route::get('/appointments', function () {
    return Inertia::render('Appointments', [
        'user' => auth()->user()
    ]);
})->middleware(['auth', 'verified', 'role:admin,Tenant'])->name('appointments');

Route::get('/appointment-request', function () {
    return Inertia::render('AppointmentRequest', [
        'user' => auth()->user()
    ]);
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('appointmentRequest');

//Maintenance
Route::get('/maintenance', function () {
    return Inertia::render('Maintenance/ShowMaintenance');
})->middleware([])->name('maintenance');

Route::get('/maintenance/new', function () {
    return Inertia::render('Maintenance/CreateMaintenance');
})->middleware(['auth', 'verified', 'role:admin,Tenant,Owner'])->name('maintenanceNew');

//MaintenanceOwner
Route::get('/maintenanceOwner', function () {
    return Inertia::render('Maintenance/ShowMaintenanceJobs');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('maintenanceOwner');

// Invoices
Route::get('/invoices', function () {
    return Inertia::render('Invoice/Invoices');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('invoices');

Route::get('/my-invoices', function () {
    return Inertia::render('Invoice/MyInvoices');
})->middleware(['auth', 'verified', 'role:admin,Tenant'])->name('myInvoices');

// Ruta para autenticación de broadcasting
Route::post('/broadcasting/auth', [BroadcastController::class, 'authenticate'])
    ->middleware('auth');

require __DIR__ . '/auth.php';

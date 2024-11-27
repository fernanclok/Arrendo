<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContractController;
use Illuminate\Foundation\Application;
use App\Http\Controllers\MaintenanceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
Route::middleware(['auth', 'verified','role:Owner'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/settings', function () {
        return Inertia::render('Dashboard', [
            'auth' => Auth::user(),
            'childComponent' => 'Settings',
        ]);
    })->name('dashboard.settings');
});

//properties
Route::get('/properties', function () {
    return Inertia::render('Properties');
})->name('properties');

// Route::get('/detailprop', function () {
//     return Inertia::render('DetailPropertie');  ---que show con esto al que le toco
// })->name('detailproperties');

//my properties
Route::get('/my-properties', function () {
    return Inertia::render('MyProperties', [
        'user' => auth()->user()
    ]);
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('myProperties');

//search properties
Route::get('/search-properties', function () {
    return Inertia::render('SearchProperties');
})->middleware(['auth','verified','role:Tenant'])->name('searchProperties');

// contracts
Route::get('/contracts', function () {
    return Inertia::render('Contracts/showContract');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('contracts');

Route::get('/manage/contracts', function () {
    return Inertia::render('ManageContracts');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('manageContracts');


// rental applications
Route::get('/TrackRequest', function () {
    return Inertia::render('TrackRequest');
})->middleware(['auth', 'verified', 'role:Tenant'])->name('TrackRequest');

Route::get('/EvaluateRequest', function () {
    return Inertia::render('EvaluateRequest');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('EvaluateRequest');


Route::middleware('auth')->group(function () {
    Route::get('/contracts/all', [ContractController::class, 'index'])->name('contracts.index');
    Route::post('/contract', [ContractController::class, 'store'])->name('contracts.store');
});

//appointments
Route::get('/appointments', function () {
    return Inertia::render('Appointments', [
        'user' => auth()->user()
    ]);
})->middleware(['auth', 'verified', 'role:admin,Tenant,Owner'])->name('appointments');

//Maintenance
Route::get('/maintenance', function () {
    return Inertia::render('Maintenance/ShowMaintenance');
})->middleware([])->name('maintenance');

Route::get('/maintenance/create', [MaintenanceController::class, 'create'])
->name('maintenance.create');

Route::post('/maintenance/store', [MaintenanceController::class, 'store'])
->name('maintenance.store');

Route::get('/maintenance/{id}', [MaintenanceController::class, 'show'])
->name('maintenance.show');

//Registro propiedades
Route::get('/registro-propiedad', function () {
    return Inertia::render('RegistroPropiedad'); // Nombre del componente Vue 
})->name('registro.propiedad');


require __DIR__ . '/auth.php';
<?php

use App\Http\Controllers\ContractController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
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

// Dashboard
Route::middleware(['auth', 'role:Owner'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/settings', function () {
        return Inertia::render('Dashboard', [
            'auth' => Auth::user(), // Pasa los datos de autenticación
            'childComponent' => 'Settings',
        ]);
    })->name('dashboard.settings');
});





Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//properties
Route::get('/properties', function () {
    return Inertia::render('Properties');
})->name('properties');

//my properties
Route::get('/my-properties', function() {
    return Inertia::render('MyProperties', [
        'user' => auth()->user()
    ]);
})->middleware(['auth','verified','role:admin,Owner'])->name('myProperties');

Route::get('/contracts', function () {
    return Inertia::render('Contracts/showContract');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('contracts');

Route::middleware('auth')->group(function () {
    Route::get('/contracts/all', [ContractController::class, 'index'])->name('contracts.index');
    Route::post('/contract', [ContractController::class, 'store'])->name('contracts.store');
});

Route::get('/manage/contracts', function () {
    return Inertia::render('Contracts/manageContracts');
})->middleware(['auth', 'verified', 'role:admin,Owner'])->name('manageContracts');

//appoinments
Route::get('/appoinments', function () {
    return Inertia::render('Appoinments');
})->middleware(['auth', 'verified', 'role:admin,Tenant,Owner'])->name('appoinments');

require __DIR__ . '/auth.php';

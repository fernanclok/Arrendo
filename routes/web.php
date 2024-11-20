<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', ['childComponent' => 'Charts']);
})->name('dashboard');

Route::get('/dashboard/tables', function () {
    return Inertia::render('Dashboard', ['childComponent' => 'Tables']);
})->name('dashboard.tables');

Route::get('/dashboard/settings', function () {
    return Inertia::render('Dashboard', ['childComponent' => 'Settings']); // Cambiado para mantener consistencia
})->name('dashboard.settings');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//properties
Route::get('/properties', function () {
    return Inertia::render('Properties');
})->name('properties');

Route::get('/contracts', function () {
    return Inertia::render('Contracts/showContract');
});

Route::get('/manage/contracts', function () {
    return Inertia::render('Contracts/manageContracts');
})->middleware(['auth','verified','role:admin,Owner'])->name('manageContracts');

//appoinments
Route::get('/appoinments', function () {
    return Inertia::render('Appoinments');
})->middleware(['auth','verified','role:admin,Tenant,Owner'])->name('appoinments');

require __DIR__ . '/auth.php';


//Registro propiedades
Route::get('/registro-propiedad', function () {
    return Inertia::render('RegistroPropiedad'); // Nombre del componente Vue 
})->name('registro.propiedad');

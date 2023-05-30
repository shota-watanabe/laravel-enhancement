<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    Route::resource('companies.sections', \App\Http\Controllers\SectionController::class);
    Route::resource('sections.users', \App\Http\Controllers\SectionUserController::class)->only(['store', 'destroy']);
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/search_user', [\App\Http\Controllers\UserController::class, 'search_user'])->name('users.search');
    Route::get('/search_office', [\App\Http\Controllers\UserController::class, 'search_user'])->name('users.search');
});

require __DIR__.'/auth.php';

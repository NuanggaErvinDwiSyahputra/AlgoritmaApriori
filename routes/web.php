<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\VariantController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AprioriController;
use App\Http\Controllers\RekomendasiController;


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

// Route::match(['get', 'post'], '/', function () {
//     return view('dashboard.dashboard');
// })->name('dashboard');

// Route::get('/', [LoginController::class, 'dashboard'])->name('dashboard');
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/', [LoginController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/rekomendasi', function () {
        return view('rekomendasi.rekomendasi');
    })->name('rekomendasi');
    
    Route::middleware(['admin.guest'])->group(function () {
        
    });
    
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    Route::post('/menu-entry', [MenuController::class, 'store'])->name('menu-entry');
    Route::post('/menu-update/{id}', [MenuController::class, 'update'])->name('menu-update');
    Route::get('/menu/{id}', [MenuController::class, 'destroy'])->name('menu-destroy');
    Route::delete('/menu/bulk-delete', [MenuController::class, 'bulkDelete'])->name('menu.bulkDelete');
    
    Route::get('/variant', [VariantController::class, 'index'])->name('variant');
    Route::get('/addvariant', [VariantController::class, 'create'])->name('addvariant');
    Route::post('/addvariant', [VariantController::class, 'store'])->name('addvariant.store');
    Route::post('/variant-update/{id}', [VariantController::class, 'update'])->name('variant-update');
    Route::get('/variant/{id}', [VariantController::class, 'destroy'])->name('variant-destroy');
    Route::delete('/variant/bulk-delete', [VariantController::class, 'bulkDelete'])->name('variant.bulkDelete');
    
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::get('/transaksi-entry', [TransaksiController::class, 'create'])->name('transaksi-entry');
    Route::post('/transaksi-entry', [TransaksiController::class, 'store'])->name('transaksi-entry.store');
    Route::post('/transaksi-update/{id}', [TransaksiController::class, 'update'])->name('transaksi-update');
    // Route::get('/transaksi/import', [TransaksiController::class, 'showImportForm'])->name('transaksi.import.form');
    Route::post('/transaksi/import', [TransaksiController::class, 'import'])->name('transaksi.import');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('transaksi-destroy');
    Route::delete('/transaksi/bulk-delete', [TransaksiController::class, 'bulkDelete'])->name('transaksi.bulkDelete');


    // routes/web.php
Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi');
Route::get('/rekomendasi/hasil', [RekomendasiController::class, 'generate'])->name('rekomendasi.generate');
});




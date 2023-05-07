<?php

use App\Http\Controllers\BatchController;
use App\Http\Controllers\PalleController;
use App\Http\Controllers\ProductController;
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
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('paller')->controller(PalleController::class)->group(function () {
        Route::get('/', 'palleDash')->name('paller.dashboard');
        Route::post('/', 'postView')->name('paller.createView');

        Route::get('/{id}', 'getView')->name('paller.view');
        Route::patch('/{id}', 'patchView');
        Route::delete('/{id}', 'deleteView');

        Route::post('/batch', 'postBatch')->name('paller.batch.new');
        Route::post('/batch/search', 'postSearch')->name('paller.batch.search');
    });

    Route::prefix('/data')->group(function () {
        Route::controller(BatchController::class)->group(function () {
            Route::get('/batch', 'getBatches')->name('data.batches');
        });

        Route::controller(ProductController::class)->group(function() {
            Route::get('/product', 'getProducts')->name('data.products');
            Route::post('/product', 'postProduct');
            Route::post('/product/delete', 'postDelete')->name('data.products.delete');
        });
    });
});

require __DIR__ . '/auth.php';
